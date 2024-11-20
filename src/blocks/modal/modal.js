import scrollLock from 'scroll-lock';
import { makeModalFrame } from "../../js/libs/modal";

(() => {
	makeModalFrame({
		init: function(underlay) {
			underlay.setAttribute('data-scroll-lock-scrollable', '');
		},
		open: function(modal, button) {
			scrollLock.disablePageScroll();
			
			const form = this.querySelector('form.order__form');
			const wrapper = form?.closest('.order');
			const infoblock = form?.querySelector('.order__alerts');
			const fields = form?.querySelectorAll('.order__fields input');
			const successClose = wrapper?.querySelector('button.order__success-button');
			const youtubeSrc = button.parentNode.querySelector('.movies__item.active')?.dataset.youtubeSrc;
		
			// показываем видео
			if (youtubeSrc && (button.dataset.modal == '#movie')) {
				this.querySelector('iframe').src = youtubeSrc;
			}

			// отправляем почту
			if (form) {
				form.addEventListener('submit', async (e) => {
					e.preventDefault();
					wrapper.classList.add('pending');

					try {
						// отправляем данные на сервер
						await fetch('mailto.php', { method: 'POST', body: new FormData(e.target) }).then((response) => {
							if (response.ok) {
								// если сервер ответил нормально - отдаем данные
								return response.json();
							} else {
								// если все плохо - генерируем исключение
								throw new Error(response.statusText);
							}
						}).then((data) => {
							// если почта ушла
							if (data.status === 'success') {
								wrapper.classList.add('success');
								e.target.reset(); 
							// если почта не ушла
							} else {
								infoblock.innerHTML = data.text;

								if (data.errors) {
									// расставляем ошибки у полей не прошедших валидацию
									fields.forEach(field => {
										data.errors.map(error => {
											if (field.name == error) field.classList.add('error');
										});
									});
								}
							}
						});
							
					} catch (error) {
						// отображаем данные если сервер ответил не правильно
						infoblock.innerHTML = error;
					} finally {
						wrapper.classList.remove('pending');
					}
				});

				// кнопка закрытия на экране успешного завершения
				successClose.addEventListener('click', e => modal.close());
			}
		},
		close: function() {
			scrollLock.enablePageScroll();
		}
	});

})();