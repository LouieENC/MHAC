// Basic form validation for adding/updating doctor
document.querySelector('.doctor-form').addEventListener('submit', function (e) {
    const name = document.getElementById('name').value.trim();
    const email = document.getElementById('email').value.trim();
    const phone = document.getElementById('phone_number').value.trim();

    if (!name || !email || !phone) {
        alert('All fields are required!');
        e.preventDefault();
    } else if (!validateEmail(email)) {
        alert('Please enter a valid email address.');
        e.preventDefault();
    }
});

// Email validation function
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(String(email).toLowerCase());
}


// Confirm before deleting a doctor
document.querySelectorAll('.delete-doctor-link').forEach(link => {
    link.addEventListener('click', function (e) {
        if (!confirm("Are you sure you want to delete this doctor?")) {
            e.preventDefault();
        }
    });
});

// Function to open the update modal and populate it with current data
function openModal(doctorId) {
    document.getElementById('updateModal').style.display = 'block';
    // Fetch the doctor's details using AJAX
    fetch(`get_doctor.php?id=${doctorId}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('doctor_id').value = data.doctor_id;
            document.getElementById('update_name').value = data.name;
            document.getElementById('update_specialization').value = data.specialization;
            document.getElementById('update_email').value = data.email;
            document.getElementById('update_phone_number').value = data.phone_number;
            document.getElementById('update_bio').value = data.bio;
            document.getElementById('update_available_times').value = data.available_times;
            document.getElementById('update_years_of_experience').value = data.years_of_experience;
            document.getElementById('update_clinic_address').value = data.clinic_address;
        });
}

// Function to close the update modal
function closeModal() {
    document.getElementById('updateModal').style.display = 'none';
}

// Close the modal when the user clicks outside of it
window.onclick = function(event) {
    if (event.target == document.getElementById('updateModal')) {
        closeModal();
    }
}
