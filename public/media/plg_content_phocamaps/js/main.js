/*
 * @package Joomla
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *
 * @extension Phoca Maps
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

document.addEventListener("DOMContentLoaded", () => {

	// Events
	document.querySelectorAll('.pm-plg-bs-modal-button').forEach(item => {


		item.addEventListener('click', function(event) {

			event.preventDefault();
			let href = this.getAttribute('href');
			let title = this.getAttribute('data-title');

			let type = this.getAttribute('data-type');


			let modalItem = document.getElementById('pmPlgModal')
			let modalIframe = document.getElementById('pmPlgModalIframe');
			let modalTitle	= document.getElementById('pmPlgModalLabel');

			//modalItem.className = '';
			//modalItem.classList.add('modal', 'fade', 'show', type);
			modalIframe.src = href;
			modalTitle.innerHTML = title;

			//let modal = document.getElementById('phCategoryModal')

			/*modal.addEventListener('shown.bs.modal', function () {
			myInput.focus()
			})*/

			let modal = new bootstrap.Modal(modalItem);
			modal.show();

		})
	})
});


