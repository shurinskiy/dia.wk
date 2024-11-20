import { driveTabs } from "../../js/libs/driveTabs";

(() => {

	document.querySelectorAll('.movies').forEach((context) => {
		const buttons = context.querySelectorAll('.movies__button');
		
		context.querySelectorAll('.movies .movies__item').forEach((item, i) => {
			item.addEventListener('click', e => buttons[i].dispatchEvent(new Event('click')));
		});

		context.addEventListener('swiped-right', (e) => buttons[0].dispatchEvent(new Event('click')));
		context.addEventListener('swiped-left', (e) => buttons[1].dispatchEvent(new Event('click')));
	});

	driveTabs({
		container: '.movies',
		controls: '.movies__button',
		selects: '.movies__item',
		cls: 'active'
	});

})();