jQuery(document).ready(function ($) {
    // Check if dark mode preference is stored in localStorage
    var isDarkMode = localStorage.getItem('darkMode') === 'true';

    // Set initial dark mode state
    if (isDarkMode) {
        $('body').addClass('darkmode');
        $('#dark-mode-toggle').html('<i class="fa fa-sun"></i>'); // Change icon to sun
    }

    // Toggle dark mode on button click
    $('#dark-mode-toggle').on('click', function () {
        // Toggle the class on the body
        $('body').toggleClass('darkmode');

        // Update icon based on dark mode state
        if ($('body').hasClass('darkmode')) {
            $('#dark-mode-toggle').html('<i class="fa fa-sun"></i>'); // Change icon to sun
        } else {
            $('#dark-mode-toggle').html('<i class="fa fa-moon"></i>'); // Change icon to moon
        }

        // Update dark mode preference in localStorage
        isDarkMode = $('body').hasClass('darkmode');
        localStorage.setItem('darkMode', isDarkMode);
    });
});
