import Swiper from 'swiper';
import { Pagination } from 'swiper/modules';
import enquire from 'enquire.js';

(() => {
	const slider = document.querySelector('.speakers__slider.swiper');
	let swiper;

	const enableSwiper = (el) => {
		swiper = new Swiper(el, {
			modules: [Pagination],
			watchOverflow: true,
			slidesPerView: 2,
			spaceBetween: 24,
			threshold: 10,
			pagination: {
				el: `.speakers__dots`,
				bulletClass: 'speakers__dot',
				bulletActiveClass: 'active',
				clickable: true
			},
			breakpoints: {
				0: {
					spaceBetween: 16,
					slidesPerView: 1
				},
				641: {
					spaceBetween: 20,
					slidesPerView: 2
				},
			}
		});
	}
		
	enquire.register("screen and (max-width: 1100px", {
		match: function() {
			enableSwiper(slider);
		},
		unmatch: function() {
			if (swiper !== undefined ) {
				swiper.destroy(true, true);
			} 
		}
	});

})();
