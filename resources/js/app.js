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
    // Add event listeners to all phone number links for opening the modal
    document.querySelectorAll('.phone-number-link').forEach(link => {
        link.addEventListener('click', function() {
            const phoneNumber = this.getAttribute('data-phone');
            const person = this.getAttribute('data-person');

            // Update modal buttons with the correct phone number
            document.getElementById('callBtn').setAttribute('onclick', `window.location.href = 'tel:${phoneNumber}'`);
            document.getElementById('whatsappBtn').setAttribute('onclick', `window.open('https://wa.me/${phoneNumber}', '_blank')`);
            
            // Update the modal heading to show the person's name
            document.querySelector('#phoneActionModal h3').textContent = `${person}`;

            // Show the modal
            document.getElementById('phoneActionModal').style.display = 'block';
        });
        closeModal();
    });

    // Add event listener to close the modal when the close button is clicked
    document.querySelector('.close-btn').addEventListener('click', function() {
        closeModal();
    });

    // Optional: Close the modal if the background is clicked
    document.getElementById('phoneActionModal').addEventListener('click', function(event) {
        if (event.target === this) {
            closeModal();
        }
    });
});

// Function to close the modal
function closeModal() {
    document.getElementById('phoneActionModal').style.display = 'none';
}

