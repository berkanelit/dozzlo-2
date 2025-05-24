
/**
 * Minecraft Server List Frontend Scripts
 */
(function($) {
    'use strict';

    // Copy server IP to clipboard
    $('.server-ip-copy, .ip-copy').on('click', function() {
        const ip = $(this).data('clipboard');
        
        if (navigator.clipboard && ip) {
            navigator.clipboard.writeText(ip).then(function() {
                // Success message
                console.log('IP copied to clipboard');
            }).catch(function(err) {
                // Error message
                console.error('Could not copy text: ', err);
            });
        }
    });

    // Initialize tooltips if available
    if (typeof tippy === 'function') {
        tippy('[title]', {
            arrow: true,
            animation: 'fade'
        });
    }
    
    // Vote form handling
    $('.vote-form').on('submit', function(e) {
        e.preventDefault();
        
        const form = $(this);
        const serverId = form.find('input[name="server_id"]').val();
        const voteCookie = 'mc_server_voted_' + serverId;
        
        // Check if already voted using local storage
        if (localStorage.getItem(voteCookie)) {
            alert(minecraft_server_list.vote_already);
            return;
        }
        
        // Submit vote via AJAX
        $.ajax({
            url: minecraft_server_list.ajax_url,
            type: 'POST',
            data: {
                action: 'vote_server',
                server_id: serverId,
                nonce: minecraft_server_list.nonce
            },
            success: function(response) {
                if (response.success) {
                    // Update vote count on page
                    const voteCount = $('.server-votes-' + serverId + ' .stat-value');
                    if (voteCount.length) {
                        voteCount.text(response.data.votes);
                    }
                    
                    // Show success message
                    alert(response.data.message);
                    
                    // Store vote in local storage
                    localStorage.setItem(voteCookie, Date.now());
                } else {
                    // Show error message
                    alert(response.data.message);
                }
            },
            error: function() {
                alert(minecraft_server_list.vote_error);
            }
        });
    });
    
    // Toggle server details on mobile
    $('.toggle-server-details').on('click', function() {
        $(this).closest('.server-card').toggleClass('details-expanded');
    });

})(jQuery);
