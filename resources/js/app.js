import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

//create and edit.blade.php in students
$(document).ready(function() {
    $('#classSelect').select2({
        placeholder: "",
        allowClear: true
    });

    // Function to generate new phone number fields
    function generatePhoneField() {
        return `
            <div class="phone-number-group mt-3">
                <label>Contact Number:</label>
                <select name="country_codes[]" class="form-control @error('country_codes.*') is-invalid @enderror">
                    <option value="+60" selected>+60</option>
                </select>
                <input type="text" name="hp_numbers[]" class="form-control" required>
                <label class="mt-2">Contact Name:</label>
                <input type="text" name="phone_persons[]" class="form-control mt-2" required>
                <button type="button" class="btn btn-danger remove-phone-number mt-2">Remove</button>
            </div>
        `;
    }

    // Add a new phone number field when the button is clicked
    $('.add-phone-number').click(function() {
        $('#phoneNumbers').append(generatePhoneField());
    });

    // Remove a phone number field
    $(document).on('click', '.remove-phone-number', function() {
        $(this).closest('.phone-number-group').remove();
    });
});

//index.blade.php in students
document.addEventListener("DOMContentLoaded", function() {
    const modal = document.getElementById('phoneActionModal');
    const closeButton = document.querySelector('.close-btn');
    const callBtn = document.getElementById('callBtn');
    const whatsappBtn = document.getElementById('whatsappBtn');

    if (!modal || !closeButton || !callBtn || !whatsappBtn) {
        console.error("Modal or buttons not found in the document.");
        return;
    }

    // Add event listeners to all phone number links
    document.querySelectorAll('.phone-number-link').forEach(link => {
        link.addEventListener('click', function() {
            const phoneNumber = this.getAttribute('data-phone');
            const person = this.getAttribute('data-person');

            // Update buttons with phone number
            callBtn.setAttribute('onclick', `window.location.href = 'tel:${phoneNumber}'`);
            whatsappBtn.setAttribute('onclick', `window.open('https://wa.me/${phoneNumber}', '_blank')`);

            // Update modal heading
            modal.querySelector('h3').textContent = person;

            // Show the modal
            modal.style.display = 'block';
        });
    });

    // Close modal when clicking the close button
    closeButton.addEventListener('click', closeModal);

    // Close modal when clicking outside the modal content
    modal.addEventListener('click', function(event) {
        if (event.target === modal) {
            closeModal();
        }
    });

    // Close modal after clicking call or WhatsApp
    callBtn.addEventListener('click', closeModal);
    whatsappBtn.addEventListener('click', closeModal);
});

// Function to close the modal
function closeModal() {
    const modal = document.getElementById('phoneActionModal');
    if (modal) {
        modal.style.display = 'none';
    }
}