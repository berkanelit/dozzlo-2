<?php
/*
Template Name: Minecraft Server Detail
*/

// Get the server ID
$server_id = get_the_ID();

// Check if the server exists and is approved
$server_approved = get_post_meta($server_id, 'server_approved', true);

if (!$server_id || $server_approved !== 'approved') {
    wp_redirect(home_url('/minecraft-server-list/'));
    exit;
}

// Get server meta data - from the cached status data
$server_status_data = get_post_meta($server_id, 'server_status_data', true);

// Get basic server info
$server_ip = get_post_meta($server_id, 'server_ip', true);
$server_java_ip = get_post_meta($server_id, 'server_java_ip', true) ?: $server_ip;
$server_bedrock_ip = get_post_meta($server_id, 'server_bedrock_ip', true) ?: $server_ip;
$server_port = get_post_meta($server_id, 'server_port', true) ?: '25565';
$server_bedrock_port = get_post_meta($server_id, 'server_bedrock_port', true) ?: '19132';

// Get server editions
$server_editions = get_post_meta($server_id, 'server_editions', true);
if (!is_array($server_editions)) {
    // Handle legacy data
    $legacy_category = get_post_meta($server_id, 'server_category', true);
    if (!empty($legacy_category)) {
        $server_editions = array($legacy_category);
    } else {
        $server_editions = array();
    }
}

// Determine which edition badge to show
$edition_badge = '';
if (in_array('java', $server_editions) && in_array('bedrock', $server_editions)) {
    $edition_badge = 'java_bedrock';
} elseif (in_array('java', $server_editions)) {
    $edition_badge = 'java';
} elseif (in_array('bedrock', $server_editions)) {
    $edition_badge = 'bedrock';
}

$server_types_array = get_post_meta($server_id, 'server_type', true);
$server_types_array = is_array($server_types_array) ? $server_types_array : array($server_types_array);
$server_version = get_post_meta($server_id, 'server_version', true);
$server_country = get_post_meta($server_id, 'server_country', true);
$server_rank = get_post_meta($server_id, 'server_rank', true) ?: 0;
$server_votes = get_post_meta($server_id, 'server_votes', true) ?: 0;
$server_rating = get_post_meta($server_id, 'server_rating', true) ?: 0;
$server_review_count = get_post_meta($server_id, 'server_review_count', true) ?: 0;
$server_discord = get_post_meta($server_id, 'server_discord', true);
$server_website = get_post_meta($server_id, 'server_website', true);
$server_featured = get_post_meta($server_id, 'server_featured', true) == 'yes';
$server_sponsored = get_post_meta($server_id, 'server_sponsored', true) == 'yes';
$server_premium = get_post_meta($server_id, 'server_premium', true) == 'yes';
$server_banner = get_post_meta($server_id, 'server_banner', true);
$server_status = get_post_meta($server_id, 'server_status', true) ?: 'offline';
$server_submission_date = get_post_meta($server_id, 'server_submission_date', true);
$server_last_checked = get_post_meta($server_id, 'server_last_checked', true);

// Edition-specific status information
$java_status = get_post_meta($server_id, 'server_java_status', true) ?: 'offline';
$java_player_count = get_post_meta($server_id, 'server_java_player_count', true) ?: 0;
$java_max_players = get_post_meta($server_id, 'server_java_max_players', true) ?: 0;
$java_version = get_post_meta($server_id, 'server_java_version', true) ?: $server_version;
$java_motd = get_post_meta($server_id, 'server_java_motd', true) ?: '';

$bedrock_status = get_post_meta($server_id, 'server_bedrock_status', true) ?: 'offline';
$bedrock_player_count = get_post_meta($server_id, 'server_bedrock_player_count', true) ?: 0;
$bedrock_max_players = get_post_meta($server_id, 'server_bedrock_max_players', true) ?: 0;
$bedrock_version = get_post_meta($server_id, 'server_bedrock_version', true) ?: $server_version;
$bedrock_motd = get_post_meta($server_id, 'server_bedrock_motd', true) ?: '';

// Calculate total players
$server_player_count = $java_player_count + $bedrock_player_count;
$server_max_players = $java_max_players + $bedrock_max_players;

// Format last checked time
$last_checked_display = !empty($server_last_checked) ? human_time_diff(strtotime($server_last_checked), current_time('timestamp')) . ' ago' : 'Never';

// Define static arrays for dropdown options
$server_categories = array(
    'java' => 'Java Edition',
    'bedrock' => 'Bedrock Edition',
    'java_bedrock' => 'Java & Bedrock Edition'
);

$server_types = array(
    'survival' => 'Survival',
    'creative' => 'Creative',
    'skyblock' => 'Skyblock',
    'factions' => 'Factions',
    'minigames' => 'Minigames',
    'prison' => 'Prison',
    'pvp' => 'PvP',
    'towny' => 'Towny',
    'pixelmon' => 'Pixelmon',
    'vanilla' => 'Vanilla',
    'modded' => 'Modded',
    'hardcore' => 'Hardcore',
    'anarchy' => 'Anarchy',
    'economy' => 'Economy',
    'roleplay' => 'Roleplay',
    'adventure' => 'Adventure',
    'smp' => 'SMP',
    'craftbukkit' => 'CraftBukkit',
    'spigot' => 'Spigot',
    'paper' => 'Paper',
    'forge' => 'Forge',
    'fabric' => 'Fabric',
    'ftb' => 'FTB',
    'tekkit' => 'Tekkit',
    'realms' => 'Realms',
    'crossplay' => 'Crossplay',
    'other' => 'Other'
);

$server_countries = array(
    'us' => 'United States',
    'ca' => 'Canada',
    'uk' => 'United Kingdom',
    'de' => 'Germany',
    'fr' => 'France',
    'au' => 'Australia',
    'br' => 'Brazil',
    'ru' => 'Russia',
    'jp' => 'Japan',
    'kr' => 'South Korea',
    'cn' => 'China',
    'in' => 'India',
    'es' => 'Spain',
    'it' => 'Italy',
    'nl' => 'Netherlands',
    'se' => 'Sweden',
    'no' => 'Norway',
    'fi' => 'Finland',
    'dk' => 'Denmark',
    'pl' => 'Poland',
    'tr' => 'Turkey',
    'mx' => 'Mexico',
    'sg' => 'Singapore',
    'ua' => 'Ukraine',
    'za' => 'South Africa',
    'ar' => 'Argentina',
    'ch' => 'Switzerland',
    'at' => 'Austria',
    'be' => 'Belgium',
    'gr' => 'Greece',
    'pt' => 'Portugal',
    'il' => 'Israel',
    'ie' => 'Ireland',
    'nz' => 'New Zealand',
    'hk' => 'Hong Kong',
    'other' => 'Other'
);

$server_versions = array(
    '1.22' => '1.22',
    '1.21' => '1.21',
    '1.20' => '1.20',
    '1.19' => '1.19',
    '1.18' => '1.18',
    '1.17' => '1.17',
    '1.16' => '1.16',
    '1.15' => '1.15',
    '1.14' => '1.14',
    '1.13' => '1.13',
    '1.12' => '1.12',
    '1.11' => '1.11',
    '1.10' => '1.10',
    '1.9' => '1.9',
    '1.8' => '1.8',
    '1.7' => '1.7',
    'bedrock_latest' => 'Bedrock Latest',
    'bedrock_legacy' => 'Bedrock Legacy'
);

// Format server types for display
$display_types = array();
foreach ($server_types_array as $type) {
    if (isset($server_types[$type])) {
        $display_types[] = $server_types[$type];
    }
}

// Prepare banner image
$banner_url = '';
if (!empty($server_banner)) {
    $banner_url = wp_get_attachment_url($server_banner);
} else if (has_post_thumbnail()) {
    $banner_url = get_the_post_thumbnail_url(get_the_ID(), 'full');
} else {
    $banner_url = 'https://via.placeholder.com/1200x400.png?text=No+Banner';
}

// Process server vote
if (isset($_POST['vote_for_server']) && isset($_POST['minecraft_server_vote_nonce']) && wp_verify_nonce($_POST['minecraft_server_vote_nonce'], 'vote_for_server')) {
    $voted_cookie = 'mc_server_voted_' . $server_id;
    $can_vote = true;
    
    if (isset($_COOKIE[$voted_cookie])) {
        $can_vote = false;
        $vote_message = '<div class="notification error">You have already voted for this server today. Please come back tomorrow.</div>';
    } else {
        $votes = (int) get_post_meta($server_id, 'server_votes', true);
        $votes++;
        update_post_meta($server_id, 'server_votes', $votes);
        setcookie($voted_cookie, '1', time() + 86400, '/');
        $vote_message = '<div class="notification success">Thank you for your vote!</div>';
        $server_votes = $votes;
    }
}

// Process review submission
$review_submitted = false;
$review_error = '';

if (isset($_POST['submit_review']) && isset($_POST['minecraft_server_review_nonce']) && wp_verify_nonce($_POST['minecraft_server_review_nonce'], 'submit_review')) {
    $review_author = isset($_POST['review_author']) ? sanitize_text_field($_POST['review_author']) : '';
    $review_email = isset($_POST['review_email']) ? sanitize_email($_POST['review_email']) : '';
    $review_rating = isset($_POST['review_rating']) ? intval($_POST['review_rating']) : 0;
    $review_content = isset($_POST['review_content']) ? wp_kses_post($_POST['review_content']) : '';
    
    if (empty($review_author)) {
        $review_error = 'Please enter your name.';
    } elseif (empty($review_email)) {
        $review_error = 'Please enter a valid email address.';
    } elseif ($review_rating < 1 || $review_rating > 5) {
        $review_error = 'Please select a rating between 1 and 5 stars.';
    } elseif (empty($review_content)) {
        $review_error = 'Please enter your review comment.';
    } else {
        $review_data = array(
            'comment_post_ID' => $server_id,
            'comment_author' => $review_author,
            'comment_author_email' => $review_email,
            'comment_content' => $review_content,
            'comment_type' => 'review',
            'comment_meta' => array('rating' => $review_rating),
            'comment_approved' => 1
        );
        
        $comment_id = wp_insert_comment($review_data);
        
        if ($comment_id) {
            add_comment_meta($comment_id, 'rating', $review_rating);
            
            $reviews = get_comments(array(
                'post_id' => $server_id,
                'type' => 'review',
                'status' => 'approve'
            ));
            
            $total_rating = 0;
            foreach ($reviews as $review) {
                $rating = get_comment_meta($review->comment_ID, 'rating', true);
                $total_rating += intval($rating);
            }
            
            $new_rating = count($reviews) > 0 ? $total_rating / count($reviews) : 0;
            update_post_meta($server_id, 'server_rating', $new_rating);
            update_post_meta($server_id, 'server_review_count', count($reviews));
            
            $server_rating = $new_rating;
            $server_review_count = count($reviews);
            
            $review_submitted = true;
        } else {
            $review_error = 'Error submitting review. Please try again.';
        }
    }
}

// Get related servers based on server types
$related_servers_args = array(
    'post_type' => 'minecraft_server',
    'posts_per_page' => 4,
    'post__not_in' => array($server_id),
    'meta_query' => array(
        'relation' => 'AND',
        array(
            'key' => 'server_approved',
            'value' => 'approved',
            'compare' => '='
        ),
        array(
            'key' => 'server_type',
            'value' => serialize($server_types_array[0]),
            'compare' => 'LIKE'
        )
    ),
    'orderby' => 'meta_value_num',
    'meta_key' => 'server_rank',
    'order' => 'ASC'
);

$related_servers = new WP_Query($related_servers_args);

// Get server reviews
$reviews = get_comments(array(
    'post_id' => $server_id,
    'type' => 'review',
    'status' => 'approve'
));

// Check overall server status
$is_online = ($server_status === 'online');

get_header();
?>

<div class="minecraft-server-detail">
    <!-- Server Hero Banner -->
    <div class="server-hero" style="background-image: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.8)), url('<?php echo esc_url($banner_url); ?>');">
        <div class="container">
            <div class="server-hero-content">
                <?php if ($server_featured || $server_sponsored || $server_premium) : ?>
                    <div class="server-badges">
                        <?php if ($server_featured) : ?>
                            <div class="server-badge featured" title="Featured Server">
                                <i class="fas fa-star"></i> Featured
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($server_sponsored) : ?>
                            <div class="server-badge sponsored" title="Sponsored Server">
                                <i class="fas fa-ad"></i> Sponsored
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($server_premium) : ?>
                            <div class="server-badge premium" title="Premium Server">
                                <i class="fas fa-gem"></i> Premium
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
                <div class="server-main-info">
                    <div class="server-logo">
                        <?php 
                        if (has_post_thumbnail()) {
                            the_post_thumbnail('thumbnail', array('class' => 'server-thumbnail'));
                        } else {
                            echo '<div class="server-placeholder">' . esc_html(substr(get_the_title(), 0, 2)) . '</div>';
                        }
                        ?>
                    </div>
                    
                    <div class="server-title-info">
                        <h1 class="server-title"><?php the_title(); ?></h1>
                        
                        <div class="server-meta">
                            <div class="server-edition <?php echo esc_attr($edition_badge); ?>">
                                <?php echo esc_html($server_categories[$edition_badge] ?? ''); ?>
                            </div>
                            
                            <div class="server-version">
                                <i class="fas fa-code-branch"></i>
                                <?php echo esc_html($server_versions[$server_version] ?? $server_version); ?>
                            </div>
                            
                            <?php if (!empty($server_country) && isset($server_countries[$server_country])) : ?>
                                <div class="server-country">
                                    <img src="" data-country="<?php echo esc_attr($server_country); ?>" alt="<?php echo esc_attr($server_countries[$server_country]); ?>" class="country-flag">
                                    <span><?php echo esc_html($server_countries[$server_country]); ?></span>
                                </div>
                            <?php endif; ?>
                            
                            <div class="server-status <?php echo $is_online ? 'online' : 'offline'; ?>">
                                <div class="status-indicator"></div>
                                <span class="status-text"><?php echo$is_online ? 'Online' : 'Offline'; ?></span>
                               <span class="status-last-checked" title="Last checked: <?php echo esc_attr($last_checked_display); ?>">
                                   <i class="fas fa-history"></i>
                               </span>
                           </div>
                       </div>
                       
                       <div class="server-tags">
                           <?php foreach ($display_types as $type_label) : ?>
                               <span class="server-tag"><?php echo esc_html($type_label); ?></span>
                           <?php endforeach; ?>
                       </div>
                   </div>
               </div>
               
               <div class="server-actions-bar">
                   <div class="server-rating-box">
                       <div class="rating-stars" title="<?php echo esc_attr(number_format($server_rating, 1)); ?> out of 5 stars">
                           <?php
                           $rating_floor = floor($server_rating);
                           $rating_decimal = $server_rating - $rating_floor;
                           
                           for ($i = 1; $i <= 5; $i++) {
                               if ($i <= $rating_floor) {
                                   echo '<i class="fas fa-star"></i>';
                               } elseif ($i == $rating_floor + 1 && $rating_decimal >= 0.5) {
                                   echo '<i class="fas fa-star-half-alt"></i>';
                               } else {
                                   echo '<i class="far fa-star"></i>';
                               }
                           }
                           ?>
                           <span class="rating-value"><?php echo number_format($server_rating, 1); ?></span>
                       </div>
                       <div class="review-count"><?php echo esc_html($server_review_count); ?> <?php echo $server_review_count === 1 ? 'review' : 'reviews'; ?></div>
                   </div>
                   
                   <div class="server-stats">
                       <div class="server-stat">
                           <i class="fas fa-thumbs-up"></i>
                           <span class="stat-label">Votes</span>
                           <span class="stat-value"><?php echo esc_html($server_votes); ?></span>
                       </div>
                       
                       <div class="server-stat">
                           <i class="fas fa-users"></i>
                           <span class="stat-label">Players</span>
                           <span class="stat-value"><?php echo esc_html($server_player_count); ?></span>
                       </div>
                       
                       <div class="server-stat">
                           <i class="fas fa-hashtag"></i>
                           <span class="stat-label">Rank</span>
                           <span class="stat-value"><?php echo esc_html($server_rank); ?></span>
                       </div>
                   </div>
                   
                   <div class="server-primary-actions">
                       <div class="server-connection-info">
                           <?php if (in_array('java', $server_editions)) : ?>
                               <div class="server-ip-copy" data-clipboard="<?php echo esc_attr($server_java_ip . ($server_port != '25565' ? ':' . $server_port : '')); ?>">
                                   <span class="ip-label">Java IP</span>
                                   <div class="ip-value">
                                       <span class="ip-text"><?php echo esc_html($server_java_ip . ($server_port != '25565' ? ':' . $server_port : '')); ?></span>
                                       <button type="button" class="copy-ip-btn">
                                           <i class="fas fa-copy"></i> Copy
                                       </button>
                                   </div>
                               </div>
                           <?php endif; ?>
                           
                           <?php if (in_array('bedrock', $server_editions)) : ?>
                               <div class="server-ip-copy" data-clipboard="<?php echo esc_attr($server_bedrock_ip); ?>">
                                   <span class="ip-label">Bedrock IP</span>
                                   <div class="ip-value">
                                       <span class="ip-text"><?php echo esc_html($server_bedrock_ip); ?></span>
                                       <button type="button" class="copy-ip-btn">
                                           <i class="fas fa-copy"></i> Copy
                                       </button>
                                   </div>
                                   <div class="bedrock-port">
                                       <span class="port-label">Port: </span>
                                       <span class="port-value"><?php echo esc_html($server_bedrock_port); ?></span>
                                   </div>
                               </div>
                           <?php endif; ?>
                       </div>
                       
                       <div class="button-group">
                           <form method="post" action="#vote-section" class="vote-form">
                               <?php wp_nonce_field('vote_for_server', 'minecraft_server_vote_nonce'); ?>
                               <input type="hidden" name="server_id" value="<?php echo esc_attr($server_id); ?>">
                               <button type="submit" name="vote_for_server" class="btn btn-vote">
                                   <i class="fas fa-thumbs-up"></i> Vote
                               </button>
                           </form>
                           
                           <?php if (in_array('bedrock', $server_editions)) : ?>
                               <a href="minecraft://connect/<?php echo esc_attr($server_bedrock_ip . ':' . $server_bedrock_port); ?>" class="btn btn-join">
                                   <i class="fas fa-play"></i> Join Server
                               </a>
                           <?php endif; ?>
                           
                           <button type="button" class="btn btn-refresh" id="refresh-server-status" data-server-id="<?php echo esc_attr($server_id); ?>">
                               <i class="fas fa-sync-alt"></i> Refresh Status
                           </button>
                           
                           <a href="#reviews" class="btn btn-review">
                               <i class="fas fa-star"></i> Write Review
                           </a>
                       </div>
                   </div>
               </div>
           </div>
       </div>
   </div>
   
   <!-- Main Content -->
   <div class="server-content-section">
       <div class="container">
           <div class="server-content-grid">
               <div class="server-main-content">
                   <?php if (isset($vote_message)): ?>
                       <div id="vote-section"><?php echo $vote_message; ?></div>
                   <?php endif; ?>
                   
                   <!-- Server Description -->
                   <div class="content-card server-description-card">
                       <div class="card-header">
                           <h2><i class="fas fa-info-circle"></i> Server Description</h2>
                       </div>
                       <div class="card-body">
                           <div class="server-description">
                               <?php 
                               $post = get_post($server_id);
                               echo !empty(trim($post->post_content)) ? apply_filters('the_content', $post->post_content) : '<p>No description available for this server.</p>';
                               ?>
                           </div>
                       </div>
                   </div>
                   
                   <!-- Server Status Details -->
                   <?php if (in_array('java', $server_editions) && in_array('bedrock', $server_editions)) : ?>
                   <div class="content-card server-status-details-card">
                       <div class="card-header">
                           <h2><i class="fas fa-signal"></i> Server Status Details</h2>
                           <div class="card-header-actions">
                               <span class="status-last-checked-text">Last checked: <?php echo esc_html($last_checked_display); ?></span>
                               <button type="button" class="btn btn-sm btn-refresh" id="refresh-status-details" data-server-id="<?php echo esc_attr($server_id); ?>">
                                   <i class="fas fa-sync-alt"></i> Refresh
                               </button>
                           </div>
                       </div>
                       <div class="card-body">
                           <div class="server-status-tabs">
                               <div class="status-tab-headers">
                                   <div class="status-tab-header active" data-tab="java">
                                       <i class="fas fa-desktop"></i> Java Edition
                                   </div>
                                   <div class="status-tab-header" data-tab="bedrock">
                                       <i class="fas fa-mobile-alt"></i> Bedrock Edition
                                   </div>
                               </div>
                               
                               <div class="status-tab-content">
                                   <div class="status-tab-pane active" id="java-status-tab">
                                       <div class="edition-status <?php echo ($java_status === 'online') ? 'online' : 'offline'; ?>">
                                           <div class="edition-status-header">
                                               <div class="status-indicator"></div>
                                               <h3>Java Server Status: <?php echo ($java_status === 'online') ? 'Online' : 'Offline'; ?></h3>
                                           </div>
                                           
                                           <?php if ($java_status === 'online') : ?>
                                           <div class="edition-status-details">
                                               <div class="status-item">
                                                   <span class="status-item-label">Players:</span>
                                                   <span class="status-item-value"><?php echo esc_html($java_player_count); ?> / <?php echo esc_html($java_max_players); ?></span>
                                               </div>
                                               <div class="status-item">
                                                   <span class="status-item-label">Version:</span>
                                                   <span class="status-item-value"><?php echo esc_html($java_version); ?></span>
                                               </div>
                                               <div class="status-item">
                                                   <span class="status-item-label">IP Address:</span>
                                                   <span class="status-item-value ip-copy" data-clipboard="<?php echo esc_attr($server_java_ip . ($server_port != '25565' ? ':' . $server_port : '')); ?>">
                                                       <?php echo esc_html($server_java_ip . ($server_port != '25565' ? ':' . $server_port : '')); ?>
                                                       <button class="copy-btn"><i class="fas fa-copy"></i></button>
                                                   </span>
                                               </div>
                                               <?php if (!empty($java_motd)) : ?>
                                               <div class="status-item">
                                                   <span class="status-item-label">MOTD:</span>
                                                   <span class="status-item-value motd"><?php echo esc_html($java_motd); ?></span>
                                               </div>
                                               <?php endif; ?>
                                           </div>
                                           <?php else : ?>
                                           <div class="edition-status-message">
                                               <p>The Java Edition server is currently offline. Please check back later or contact the server administrator.</p>
                                           </div>
                                           <?php endif; ?>
                                       </div>
                                   </div>
                                   
                                   <div class="status-tab-pane" id="bedrock-status-tab">
                                       <div class="edition-status <?php echo ($bedrock_status === 'online') ? 'online' : 'offline'; ?>">
                                           <div class="edition-status-header">
                                               <div class="status-indicator"></div>
                                               <h3>Bedrock Server Status: <?php echo ($bedrock_status === 'online') ? 'Online' : 'Offline'; ?></h3>
                                           </div>
                                           
                                           <?php if ($bedrock_status === 'online') : ?>
                                           <div class="edition-status-details">
                                               <div class="status-item">
                                                   <span class="status-item-label">Players:</span>
                                                   <span class="status-item-value"><?php echo esc_html($bedrock_player_count); ?> / <?php echo esc_html($bedrock_max_players); ?></span>
                                               </div>
                                               <div class="status-item">
                                                   <span class="status-item-label">Version:</span>
                                                   <span class="status-item-value"><?php echo esc_html($bedrock_version); ?></span>
                                               </div>
                                               <div class="status-item">
                                                   <span class="status-item-label">IP Address:</span>
                                                   <span class="status-item-value ip-copy" data-clipboard="<?php echo esc_attr($server_bedrock_ip); ?>">
                                                       <?php echo esc_html($server_bedrock_ip); ?>
                                                       <button class="copy-btn"><i class="fas fa-copy"></i></button>
                                                   </span>
                                               </div>
                                               <div class="status-item">
                                                   <span class="status-item-label">Port:</span>
                                                   <span class="status-item-value"><?php echo esc_html($server_bedrock_port); ?></span>
                                               </div>
                                               <?php if (!empty($bedrock_motd)) : ?>
                                               <div class="status-item">
                                                   <span class="status-item-label">MOTD:</span>
                                                   <span class="status-item-value motd"><?php echo esc_html($bedrock_motd); ?></span>
                                               </div>
                                               <?php endif; ?>
                                           </div>
                                           <?php else : ?>
                                           <div class="edition-status-message">
                                               <p>The Bedrock Edition server is currently offline. Please check back later or contact the server administrator.</p>
                                           </div>
                                           <?php endif; ?>
                                       </div>
                                   </div>
                               </div>
                           </div>
                       </div>
                   </div>
                   <?php else : ?>
                   <!-- Single Edition Status Card -->
                   <div class="content-card server-status-details-card">
                       <div class="card-header">
                           <h2><i class="fas fa-signal"></i> Server Status</h2>
                           <div class="card-header-actions">
                               <span class="status-last-checked-text">Last checked: <?php echo esc_html($last_checked_display); ?></span>
                               <button type="button" class="btn btn-sm btn-refresh" id="refresh-status-details" data-server-id="<?php echo esc_attr($server_id); ?>">
                                   <i class="fas fa-sync-alt"></i> Refresh
                               </button>
                           </div>
                       </div>
                       <div class="card-body">
                           <?php if (in_array('java', $server_editions)) : ?>
                           <div class="edition-status <?php echo ($java_status === 'online') ? 'online' : 'offline'; ?>">
                               <div class="edition-status-header">
                                   <div class="status-indicator"></div>
                                   <h3>Java Server Status: <?php echo ($java_status === 'online') ? 'Online' : 'Offline'; ?></h3>
                               </div>
                               
                               <?php if ($java_status === 'online') : ?>
                               <div class="edition-status-details">
                                   <div class="status-item">
                                       <span class="status-item-label">Players:</span>
                                       <span class="status-item-value"><?php echo esc_html($java_player_count); ?> / <?php echo esc_html($java_max_players); ?></span>
                                   </div>
                                   <div class="status-item">
                                       <span class="status-item-label">Version:</span>
                                       <span class="status-item-value"><?php echo esc_html($java_version); ?></span>
                                   </div>
                                   <div class="status-item">
                                       <span class="status-item-label">IP Address:</span>
                                       <span class="status-item-value ip-copy" data-clipboard="<?php echo esc_attr($server_java_ip . ($server_port != '25565' ? ':' . $server_port : '')); ?>">
                                           <?php echo esc_html($server_java_ip . ($server_port != '25565' ? ':' . $server_port : '')); ?>
                                           <button class="copy-btn"><i class="fas fa-copy"></i></button>
                                       </span>
                                   </div>
                                   <?php if (!empty($java_motd)) : ?>
                                   <div class="status-item">
                                       <span class="status-item-label">MOTD:</span>
                                       <span class="status-item-value motd"><?php echo esc_html($java_motd); ?></span>
                                   </div>
                                   <?php endif; ?>
                               </div>
                               <?php else : ?>
                               <div class="edition-status-message">
                                   <p>The server is currently offline. Please check back later or contact the server administrator.</p>
                               </div>
                               <?php endif; ?>
                           </div>
                           <?php endif; ?>
                           
                           <?php if (in_array('bedrock', $server_editions)) : ?>
                           <div class="edition-status <?php echo ($bedrock_status === 'online') ? 'online' : 'offline'; ?>">
                               <div class="edition-status-header">
                                   <div class="status-indicator"></div>
                                   <h3>Bedrock Server Status: <?php echo ($bedrock_status === 'online') ? 'Online' : 'Offline'; ?></h3>
                               </div>
                               
                               <?php if ($bedrock_status === 'online') : ?>
                               <div class="edition-status-details">
                                   <div class="status-item">
                                       <span class="status-item-label">Players:</span>
                                       <span class="status-item-value"><?php echo esc_html($bedrock_player_count); ?> / <?php echo esc_html($bedrock_max_players); ?></span>
                                   </div>
                                   <div class="status-item">
                                       <span class="status-item-label">Version:</span>
                                       <span class="status-item-value"><?php echo esc_html($bedrock_version); ?></span>
                                   </div>
                                   <div class="status-item">
                                       <span class="status-item-label">IP Address:</span>
                                       <span class="status-item-value ip-copy" data-clipboard="<?php echo esc_attr($server_bedrock_ip); ?>">
                                           <?php echo esc_html($server_bedrock_ip); ?>
                                           <button class="copy-btn"><i class="fas fa-copy"></i></button>
                                       </span>
                                   </div>
                                   <div class="status-item">
                                       <span class="status-item-label">Port:</span>
                                       <span class="status-item-value"><?php echo esc_html($server_bedrock_port); ?></span>
                                   </div>
                                   <?php if (!empty($bedrock_motd)) : ?>
                                   <div class="status-item">
                                       <span class="status-item-label">MOTD:</span>
                                       <span class="status-item-value motd"><?php echo esc_html($bedrock_motd); ?></span>
                                   </div>
                                   <?php endif; ?>
                               </div>
                               <?php else : ?>
                               <div class="edition-status-message">
                                   <p>The server is currently offline. Please check back later or contact the server administrator.</p>
                               </div>
                               <?php endif; ?>
                           </div>
                           <?php endif; ?>
                       </div>
                   </div>
                   <?php endif; ?>
                   
                   <!-- Server Reviews -->
                   <div id="reviews" class="content-card server-reviews-card">
                       <div class="card-header">
                           <h2><i class="fas fa-star"></i> Server Reviews</h2>
                       </div>
                       <div class="card-body">
                           <?php if (count($reviews) > 0) : ?>
                               <div class="reviews-summary">
                                   <div class="overall-rating">
                                       <div class="rating-value"><?php echo number_format($server_rating, 1); ?></div>
                                       <div class="rating-stars">
                                           <?php
                                           $rating_floor = floor($server_rating);
                                           $rating_decimal = $server_rating - $rating_floor;
                                           
                                           for ($i = 1; $i <= 5; $i++) {
                                               if ($i <= $rating_floor) {
                                                   echo '<i class="fas fa-star"></i>';
                                               } elseif ($i == $rating_floor + 1 && $rating_decimal >= 0.5) {
                                                   echo '<i class="fas fa-star-half-alt"></i>';
                                               } else {
                                                   echo '<i class="far fa-star"></i>';
                                               }
                                           }
                                           ?>
                                       </div>
                                       <div class="reviews-count"><?php echo esc_html($server_review_count); ?> <?php echo $server_review_count === 1 ? 'review' : 'reviews'; ?></div>
                                   </div>
                                   
                                   <div class="rating-breakdown">
                                       <?php
                                       $rating_counts = array(5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0);
                                       
                                       foreach ($reviews as $review) {
                                           $rating = get_comment_meta($review->comment_ID, 'rating', true);
                                           $rating = intval($rating);
                                           if ($rating >= 1 && $rating <= 5) {
                                               $rating_counts[$rating]++;
                                           }
                                       }
                                       
                                       for ($i = 5; $i >= 1; $i--) {
                                           $percentage = $server_review_count > 0 ? ($rating_counts[$i] / $server_review_count) * 100 : 0;
                                           ?>
                                           <div class="rating-bar">
                                               <div class="rating-label"><?php echo $i; ?> <i class="fas fa-star"></i></div>
                                               <div class="rating-progress">
                                                   <div class="progress-bar" style="width: <?php echo esc_attr($percentage); ?>%"></div>
                                               </div>
                                               <div class="rating-count"><?php echo $rating_counts[$i]; ?></div>
                                           </div>
                                           <?php
                                       }
                                       ?>
                                   </div>
                               </div>
                               
                               <div class="reviews-list">
                                   <?php foreach ($reviews as $review): ?>
                                       <?php
                                       $review_rating = get_comment_meta($review->comment_ID, 'rating', true);
                                       $review_date = strtotime($review->comment_date);
                                       ?>
                                       <div class="review-item">
                                           <div class="review-header">
                                               <div class="reviewer-info">
                                                   <div class="reviewer-name"><?php echo esc_html($review->comment_author); ?></div>
                                                   <div class="review-date"><?php echo date('F j, Y', $review_date); ?></div>
                                               </div>
                                               <div class="review-rating">
                                                   <?php for ($i = 1; $i <= 5; $i++): ?>
                                                       <i class="<?php echo $i <= $review_rating ? 'fas' : 'far'; ?> fa-star"></i>
                                                   <?php endfor; ?>
                                               </div>
                                           </div>
                                           <div class="review-content">
                                               <?php echo wp_kses_post($review->comment_content); ?>
                                           </div>
                                       </div>
                                   <?php endforeach; ?>
                               </div>
                           <?php else: ?>
                               <div class="no-reviews">
                                   <div class="no-reviews-icon">
                                       <i class="far fa-star"></i>
                                   </div>
                                   <h3>No Reviews Yet</h3>
                                   <p>Be the first to leave a review for this server!</p>
                               </div>
                           <?php endif; ?>
                           
                           <!-- Review Form -->
                           <div class="review-form-section">
                               <h3>Write a Review</h3>
                               
                               <?php if ($review_submitted): ?>
                                   <div class="notification success">
                                       <p>Thank you for your review! It has been published successfully.</p>
                                   </div>
                               <?php elseif (!empty($review_error)): ?>
                                   <div class="notification error">
                                       <p><?php echo esc_html($review_error); ?></p>
                                   </div>
                               <?php endif; ?>
                               
                               <form method="post" action="#reviews" class="review-form">
                                   <?php wp_nonce_field('submit_review', 'minecraft_server_review_nonce'); ?>
                                   
                                   <div class="form-row">
                                       <div class="form-group">
                                           <label for="review_author">Name <span class="required">*</span></label>
                                           <input type="text" name="review_author" id="review_author" required>
                                       </div>
                                       
                                       <div class="form-group">
                                           <label for="review_email">Email <span class="required">*</span></label>
                                           <input type="email" name="review_email" id="review_email" required>
                                           <div class="field-note">Your email will not be published</div>
                                       </div>
                                   </div>
                                   
                                   <div class="form-group">
                                       <label>Your Rating <span class="required">*</span></label>
                                       <div class="rating-selector">
                                           <?php for ($i = 5; $i >= 1; $i--): ?>
                                               <input type="radio" name="review_rating" id="rating-<?php echo $i; ?>" value="<?php echo $i; ?>" <?php checked(isset($_POST['review_rating']) && $_POST['review_rating'] == $i); ?> required>
                                               <label for="rating-<?php echo $i; ?>"><i class="far fa-star"></i></label>
                                           <?php endfor; ?>
                                       </div>
                                   </div>
                                   
                                   <div class="form-group">
                                       <label for="review_content">Your Review <span class="required">*</span></label>
                                       <textarea name="review_content" id="review_content" rows="5" required><?php echo isset($_POST['review_content']) ? esc_textarea($_POST['review_content']) : ''; ?></textarea>
                                   </div>
                                   
                                   <div class="form-actions">
                                       <button type="submit" name="submit_review" class="btn btn-submit">
                                           <i class="fas fa-paper-plane"></i> Submit Review
                                       </button>
                                   </div>
                               </form>
                           </div>
                       </div>
                   </div>
               </div>
               
               <div class="server-sidebar">
                   <!-- Server Information -->
                   <div class="content-card server-info-card">
                       <div class="card-header">
                           <h3><i class="fas fa-server"></i> Server Information</h3>
                       </div>
                       <div class="card-body">
                           <ul class="server-info-list">
                               <!-- Edition-specific IP Addresses -->
                               <?php if (in_array('java', $server_editions)) : ?>
                               <li>
                                   <div class="info-label">Java IP:</div>
                                   <div class="info-value server-ip-copy" data-clipboard="<?php echo esc_attr($server_java_ip . ($server_port != '25565' ? ':' . $server_port : '')); ?>">
                                       <span class="ip-text"><?php echo esc_html($server_java_ip . ($server_port != '25565' ? ':' . $server_port : '')); ?></span>
                                       <button type="button" class="copy-ip-btn">
                                           <i class="fas fa-copy"></i>
                                       </button>
                                   </div>
                               </li>
                               <?php endif; ?>
                               
                               <?php if (in_array('bedrock', $server_editions)) : ?>
                               <li>
                                   <div class="info-label">Bedrock IP:</div>
                                   <div class="info-value server-ip-copy" data-clipboard="<?php echo esc_attr($server_bedrock_ip); ?>">
                                       <span class="ip-text"><?php echo esc_html($server_bedrock_ip); ?></span>
                                       <button type="button" class="copy-ip-btn">
                                           <i class="fas fa-copy"></i>
                                       </button>
                                   </div>
                               </li>
                               <li>
                                   <div class="info-label">Bedrock Port:</div>
                                   <div class="info-value">
                                       <?php echo esc_html($server_bedrock_port); ?>
                                   </div>
                               </li>
                               <?php endif; ?>
                               
                               <li>
                                   <div class="info-label">Status:</div>
                                   <div class="info-value">
                                       <span class="status-indicator <?php echo $is_online ? 'online' : 'offline'; ?>"></span>
                                       <?php echo $is_online ? 'Online' : 'Offline'; ?>
                                       <span class="status-updated">updated <?php echo esc_html($last_checked_display); ?></span>
                                   </div>
                               </li>
                               
                               <li>
                                   <div class="info-label">Players:</div>
                                   <div class="info-value player-counts">
                                       <?php if (in_array('java', $server_editions) && in_array('bedrock', $server_editions)): ?>
                                           <div class="player-edition-count">
                                               <span class="edition-icon"><i class="fas fa-desktop"></i> Java:</span>
                                               <span class="player-number"><?php echo esc_html($java_player_count); ?>/<?php echo esc_html($java_max_players); ?></span>
                                           </div>
                                           <div class="player-edition-count">
                                               <span class="edition-icon"><i class="fas fa-mobile-alt"></i> Bedrock:</span>
                                               <span class="player-number"><?php echo esc_html($bedrock_player_count); ?>/<?php echo esc_html($bedrock_max_players); ?></span>
                                           </div>
                                           <div class="player-edition-count total">
                                               <span class="edition-icon"><i class="fas fa-users"></i> Total:</span>
                                               <span class="player-number"><?php echo esc_html($server_player_count); ?>/<?php echo esc_html($server_max_players); ?></span>
                                           </div>
                                       <?php else: ?>
                                           <span class="player-number"><?php echo esc_html($server_player_count); ?>/<?php echo esc_html($server_max_players); ?></span>
                                       <?php endif; ?>
                                   </div>
                               </li>
                               
                               <li>
                                   <div class="info-label">Version:</div>
                                   <div class="info-value"><?php echo esc_html($server_versions[$server_version] ?? $server_version); ?></div>
                               </li>
                               
                               <li>
                                   <div class="info-label">Edition:</div>
                                   <div class="info-value">
                                       <span class="edition-badge <?php echo esc_attr($edition_badge); ?>">
                                           <?php echo esc_html($server_categories[$edition_badge] ?? ''); ?>
                                       </span>
                                   </div>
                               </li>
                               
                               <li>
                                   <div class="info-label">Server Types:</div>
                                   <div class="info-value server-types">
                                       <?php foreach ($display_types as $type): ?>
                                           <a href="<?php echo esc_url(add_query_arg('type', array_search($type, $server_types), home_url('/minecraft-server-list/'))); ?>" class="server-type-badge">
                                               <?php echo esc_html($type); ?>
                                           </a>
                                       <?php endforeach; ?>
                                   </div>
                               </li>
                               
                               <?php if (!empty($server_country) && isset($server_countries[$server_country])): ?>
                                   <li>
                                       <div class="info-label">Country:</div>
                                       <div class="info-value country-display">
                                           <img src="" data-country="<?php echo esc_attr($server_country); ?>" alt="<?php echo esc_attr($server_countries[$server_country]); ?>" class="country-flag">
                                           <?php echo esc_html($server_countries[$server_country]); ?>
                                       </div>
                                   </li>
                               <?php endif; ?>
                               
                               <?php if (!empty($server_submission_date)): ?>
                                   <li>
                                       <div class="info-label">Added:</div>
                                       <div class="info-value">
                                           <?php echo date('F j, Y', strtotime($server_submission_date)); ?>
                                       </div>
                                   </li>
                               <?php endif; ?>
                           </ul>
                       </div>
                   </div>
                   
                   <!-- Server Links -->
                   <?php if (!empty($server_website) || !empty($server_discord)): ?>
                       <div class="content-card server-links-card">
                           <div class="card-header">
                               <h3><i class="fas fa-link"></i> Links</h3>
                           </div>
                           <div class="card-body">
                               <ul class="server-links-list">
                                   <?php if (!empty($server_website)): ?>
                                       <li>
                                           <a href="<?php echo esc_url($server_website); ?>" target="_blank" rel="nofollow noopener" class="server-link website-link">
                                               <i class="fas fa-globe"></i>
                                               <span>Official Website</span>
                                               <i class="fas fa-external-link-alt"></i>
                                           </a>
                                       </li>
                                   <?php endif; ?>
                                   
                                   <?php if (!empty($server_discord)): ?>
                                       <li>
                                           <a href="<?php echo esc_url($server_discord); ?>" target="_blank" rel="nofollow noopener" class="server-link discord-link">
                                               <i class="fab fa-discord"></i>
                                               <span>Discord Server</span>
                                               <i class="fas fa-external-link-alt"></i>
                                           </a>
                                       </li>
                                   <?php endif; ?>
                               </ul>
                           </div>
                       </div>
                   <?php endif; ?>
                   
                   <!-- Server Vote -->
                   <div class="content-card server-vote-card">
                       <div class="card-header">
                           <h3><i class="fas fa-thumbs-up"></i> Vote for Server</h3>
                       </div>
                       <div class="card-body">
                           <div class="vote-count">
                               <div class="vote-number"><?php echo esc_html($server_votes); ?></div>
                               <div class="vote-label">Total Votes</div>
                           </div>
                           
                           <form method="post" action="#vote-section" class="vote-form">
                               <?php wp_nonce_field('vote_for_server', 'minecraft_server_vote_nonce'); ?>
                               <input type="hidden" name="server_id" value="<?php echo esc_attr($server_id); ?>">
                               <button type="submit" name="vote_for_server" class="btn btn-vote-large">
                                   <i class="fas fa-thumbs-up"></i> Vote for this Server
                               </button>
                           </form>
                           
                           <div class="vote-note">
                               You can vote once every 24 hours
                           </div>
                       </div>
                   </div>
                   
                   <!-- Share Server -->
                   <div class="content-card server-share-card">
                       <div class="card-header">
                           <h3><i class="fas fa-share-alt"></i> Share Server</h3>
                       </div>
                       <div class="card-body">
                           <div class="share-buttons">
                               <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_permalink()); ?>" target="_blank" rel="noopener" class="share-button facebook">
                                   <i class="fab fa-facebook-f"></i>
                               </a>
                               
                               <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(get_permalink()); ?>&text=<?php echo urlencode('Check out ' . get_the_title() . ' Minecraft server!'); ?>" target="_blank" rel="noopener" class="share-button twitter">
                                   <i class="fab fa-twitter"></i>
                               </a>
                               
                               <a href="https://www.reddit.com/submit?url=<?php echo urlencode(get_permalink()); ?>&title=<?php echo urlencode(get_the_title() . ' - Minecraft Server'); ?>" target="_blank" rel="noopener" class="share-button reddit">
                                   <i class="fab fa-reddit-alien"></i>
                               </a>
                               
                               <a href="mailto:?subject=<?php echo urlencode('Check out this Minecraft server: ' . get_the_title()); ?>&body=<?php echo urlencode('Hey, I found this awesome Minecraft server and thought you might be interested: ' . get_permalink()); ?>" class="share-button email">
                                   <i class="fas fa-envelope"></i>
                               </a>
                           </div>
                       </div>
                   </div>

                   <!-- Join Instructions -->
                   <div class="content-card server-join-card">
                       <div class="card-header">
                           <h3><i class="fas fa-sign-in-alt"></i> How to Join</h3>
                       </div>
                       <div class="card-body join-instructions">
                           <?php if (in_array('java', $server_editions)) : ?>
                           <div class="join-section">
                               <h4><i class="fas fa-desktop"></i> Java Edition</h4>
                               <ol>
                                   <li>Open Minecraft Java Edition</li>
                                   <li>Click on "Multiplayer"</li>
                                   <li>Click "Add Server"</li>
                                   <li>Enter a server name (e.g. "<?php echo esc_html(get_the_title()); ?>")</li>
                                   <li>Enter the server address: <strong><?php echo esc_html($server_java_ip . ($server_port != '25565' ? ':' . $server_port : '')); ?></strong></li>
                                   <li>Click "Done" and then select the server to join</li>
                               </ol>
                           </div>
                           <?php endif; ?>
                           
                           <?php if (in_array('bedrock', $server_editions)) : ?>
                           <div class="join-section">
                               <h4><i class="fas fa-mobile-alt"></i> Bedrock Edition</h4>
                               <ol>
                                   <li>Open Minecraft Bedrock Edition</li>
                                   <li>Click on "Play" and then "Servers" tab</li>
                                   <li>Scroll down and click "Add Server"</li>
                                   <li>Enter a server name (e.g. "<?php echo esc_html(get_the_title()); ?>")</li>
                                   <li>Enter the server address: <strong><?php echo esc_html($server_bedrock_ip); ?></strong></li>
                                   <li>Enter the port: <strong><?php echo esc_html($server_bedrock_port); ?></strong></li>
                                   <li>Click "Save" and then select the server to join</li>
                               </ol>
                               
                               <div class="join-direct">
                                   <p>Or join directly:</p>
                                   <a href="minecraft://connect/<?php echo esc_attr($server_bedrock_ip . ':' . $server_bedrock_port); ?>" class="btn btn-join-direct">
                                       <i class="fas fa-play"></i> Join Now
                                   </a>
                                   <p class="join-note">This button works on Windows 10, iOS, and Android</p>
                               </div>
                           </div>
                           <?php endif; ?>
                       </div>
                   </div>
               </div>
           </div>
           
           <!-- Related Servers -->
           <?php if ($related_servers->have_posts()): ?>
               <div class="related-servers-section">
                   <h2><i class="fas fa-th-large"></i> Similar Servers You Might Like</h2>
                   
                   <div class="related-servers-grid">
                       <?php while ($related_servers->have_posts()): $related_servers->the_post(); ?>
                           <?php
                           $related_id = get_the_ID();
                           
                           // Get server editions
                           $related_editions = get_post_meta($related_id, 'server_editions', true);
                           if (!is_array($related_editions)) {
                               // Handle legacy data
                               $legacy_category = get_post_meta($related_id, 'server_category', true);
                               if (!empty($legacy_category)) {
                                   $related_editions = array($legacy_category);
                               } else {
                                   $related_editions = array();
                               }
                           }
                           
                           // Determine which edition badge to show
                           $related_edition_badge = '';
                           if (in_array('java', $related_editions) && in_array('bedrock', $related_editions)) {
                               $related_edition_badge = 'java_bedrock';
                           } elseif (in_array('java', $related_editions)) {
                               $related_edition_badge = 'java';
                           } elseif (in_array('bedrock', $related_editions)) {
                               $related_edition_badge = 'bedrock';
                           }
                           
                           $related_version = get_post_meta($related_id, 'server_version', true);
                           $related_banner = get_post_meta($related_id, 'server_banner', true);
                           $related_status = get_post_meta($related_id, 'server_status', true) ?: 'offline';
                           $is_related_online = ($related_status === 'online');
                           
                           // Get related server types
                           $related_server_types = get_post_meta($related_id, 'server_type', true);
                           $related_server_types = is_array($related_server_types) ? $related_server_types : array($related_server_types);
                           $related_types_display = array();
                           foreach ($related_server_types as $type) {
                               if (isset($server_types[$type])) {
                                   $related_types_display[] = $server_types[$type];
                               }
                           }
                           
                           if (!empty($related_banner)) {
                               $related_image = wp_get_attachment_url($related_banner);
                           } elseif (has_post_thumbnail($related_id)) {
                               $related_image = get_the_post_thumbnail_url($related_id, 'medium');
                           } else {
                               $related_image = 'https://via.placeholder.com/300x150.png?text=No+Banner';
                           }
                           ?>
                           
                           <div class="related-server-card">
                               <a href="<?php the_permalink(); ?>" class="related-server-link">
                                   <div class="related-server-banner" style="background-image: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.7)), url('<?php echo esc_url($related_image); ?>');">
                                       <div class="related-server-status <?php echo $is_related_online ? 'online' : 'offline'; ?>">
                                           <div class="status-indicator"></div>
                                           <span><?php echo $is_related_online ? 'Online' : 'Offline'; ?></span>
                                       </div>
                                       
                                       <div class="related-server-info">
                                           <h3 class="related-server-title"><?php the_title(); ?></h3>
                                           
                                           <div class="related-server-meta">
                                               <div class="related-server-edition <?php echo esc_attr($related_edition_badge); ?>">
                                                   <?php echo esc_html($server_categories[$related_edition_badge] ?? ''); ?>
                                               </div>
                                               
                                               <div class="related-server-version">
                                                   <i class="fas fa-code-branch"></i>
                                                   <?php echo esc_html($server_versions[$related_version] ?? $related_version); ?>
                                               </div>
                                           </div>
                                           
                                           <div class="related-server-types">
                                               <?php foreach (array_slice($related_types_display, 0, 2) as $type_label): ?>
                                                   <span class="related-server-type"><?php echo esc_html($type_label); ?></span>
                                               <?php endforeach; ?>
                                               
                                               <?php if (count($related_types_display) > 2): ?>
                                                   <span class="related-server-type more">+<?php echo (count($related_types_display) - 2); ?> more</span>
                                               <?php endif; ?>
                                           </div>
                                       </div>
                                   </div>
                               </a>
                           </div>
                       <?php endwhile; ?>
                       <?php wp_reset_postdata(); ?>
                   </div>
               </div>
           <?php endif; ?>
           
           <!-- Back to Server List -->
           <div class="back-to-servers">
               <a href="<?php echo esc_url(home_url('/minecraft-server-list/')); ?>" class="btn btn-secondary">
                   <i class="fas fa-arrow-left"></i> Back to Server List
               </a>
           </div>
       </div>
   </div>
</div>

<!-- Server Detail JavaScript -->
<script>
jQuery(document).ready(function($) {
   // Status tab functionality
   $('.status-tab-header').on('click', function() {
       const tabId = $(this).data('tab');
       $('.status-tab-header').removeClass('active');
       $(this).addClass('active');
       $('.status-tab-pane').removeClass('active');
       $('#' + tabId + '-status-tab').addClass('active');
   });
   
   // Rating selector functionality
   $('.rating-selector label').on('click', function() {
       $('.rating-selector label').removeClass('selected');
       $(this).addClass('selected').prevAll('label').addClass('selected');
   });
   
   // IP copy functionality
   $('.ip-copy, .server-ip-copy').on('click', function() {
       const ip = $(this).data('clipboard');
       const $button = $(this).find('.copy-ip-btn, .copy-btn');
       const originalHTML = $button.html();
       
       if (navigator.clipboard && navigator.clipboard.writeText) {
           navigator.clipboard.writeText(ip)
               .then(() => {
                   showCopiedFeedback($button, originalHTML);
               })
               .catch(() => {
                   fallbackCopy(ip, $button, originalHTML);
               });
       } else {
           fallbackCopy(ip, $button, originalHTML);
       }
   });
   
   function fallbackCopy(text, $button, originalHTML) {
       const $textarea = $('<textarea>').css({
           position: 'fixed',
           opacity: 0
       }).val(text);
       
       $('body').append($textarea);
       $textarea.select();
       
       try {
           const success = document.execCommand('copy');
           if (success) {
               showCopiedFeedback($button, originalHTML);
           }
       } catch (err) {
           console.error('Copy failed:', err);
       }
       
       $textarea.remove();
   }
   
   function showCopiedFeedback($button, originalHTML) {
       $button.html('<i class="fas fa-check"></i> Copied');
       setTimeout(() => {
           $button.html(originalHTML);
       }, 2000);
       
       showNotification('Server address copied to clipboard!', 'success');
   }
   
   // Refresh server status
   $('#refresh-server-status, #refresh-status-details').on('click', function() {
       const $button = $(this);
       const serverId = $button.data('server-id');
       
       if (!serverId) return;
       
       // Show loading state
       $button.prop('disabled', true)
           .addClass('refreshing')
           .html('<i class="fas fa-spinner fa-spin"></i> Refreshing...');
       
       // Make the AJAX request
       $.ajax({
           url: ajaxurl || '/wp-admin/admin-ajax.php',
           type: 'POST',
           data: {
               action: 'minecraft_server_check_status',
               server_id: serverId,
               security: '<?php echo wp_create_nonce("minecraft_server_check_nonce"); ?>'
           },
           success: function(response) {
               if (response.success) {
                   // Update the UI with new status data
                   updateServerStatusUI(response.data);
                   showNotification('Server status updated successfully!', 'success');
               } else {
                   showNotification('Error updating server status', 'error');
               }
           },
           error: function() {
               showNotification('Error checking server status', 'error');
           },
           complete: function() {
               // Restore button state
               $button.prop('disabled', false)
                   .removeClass('refreshing')
                   .html('<i class="fas fa-sync-alt"></i> ' + ($button.attr('id') === 'refresh-server-status' ? 'Refresh Status' : 'Refresh'));
           }
       });
   });
   
   // Update server status UI with new data
   function updateServerStatusUI(data) {
       const isOnline = data.overall_status === 'online';
       
       // Update main server status
       $('.server-status')
           .removeClass('online offline')
           .addClass(isOnline ? 'online' : 'offline')
           .find('.status-text')
           .text(isOnline ? 'Online' : 'Offline');
       
       // Update last checked time
       const lastCheckedText = 'Just now';
       $('.status-last-checked, .status-last-checked-text').attr('title', 'Last checked: ' + lastCheckedText).text('Last checked: ' + lastCheckedText);
       $('.status-updated').text('updated just now');
       
       // Update Java status if applicable
       if (data.java_status) {
           $('#java-status-tab .edition-status')
               .removeClass('online offline')
               .addClass(data.java_status === 'online' ? 'online' : 'offline')
               .find('.edition-status-header h3')
               .text('Java Server Status: ' + (data.java_status === 'online' ? 'Online' : 'Offline'));
           
           if (data.java_status === 'online') {
               $('#java-status-tab .edition-status-details').show();
               $('#java-status-tab .edition-status-message').hide();
               $('#java-status-tab .status-item-value:contains("Players")').text(data.java_player_count + ' / ' + data.java_max_players);
               if (data.java_version) {
                   $('#java-status-tab .status-item-value:contains("Version")').text(data.java_version);
               }
               if (data.java_motd) {
                   if ($('#java-status-tab .status-item-value.motd').length) {
                       $('#java-status-tab .status-item-value.motd').text(data.java_motd);
                   } else {
                       $('#java-status-tab .edition-status-details').append(`
                           <div class="status-item">
                               <span class="status-item-label">MOTD:</span>
                               <span class="status-item-value motd">${data.java_motd}</span>
                           </div>
                       `);
                   }
               }
           } else {
               $('#java-status-tab .edition-status-details').hide();
               $('#java-status-tab .edition-status-message').show();
           }
           
           // Update Java player count in sidebar
           $('.player-edition-count:eq(0) .player-number').text(data.java_player_count + '/' + data.java_max_players);
       }
       
       // Update Bedrock status if applicable
       if (data.bedrock_status) {
           $('#bedrock-status-tab .edition-status')
               .removeClass('online offline')
               .addClass(data.bedrock_status === 'online' ? 'online' : 'offline')
               .find('.edition-status-header h3')
               .text('Bedrock Server Status: ' + (data.bedrock_status === 'online' ? 'Online' : 'Offline'));
           
           if (data.bedrock_status === 'online') {
               $('#bedrock-status-tab .edition-status-details').show();
               $('#bedrock-status-tab .edition-status-message').hide();
               $('#bedrock-status-tab .status-item-value:contains("Players")').text(data.bedrock_player_count + ' / ' + data.bedrock_max_players);
               if (data.bedrock_version) {
                   $('#bedrock-status-tab .status-item-value:contains("Version")').text(data.bedrock_version);
               }
               if (data.bedrock_motd) {
                   if ($('#bedrock-status-tab .status-item-value.motd').length) {
                       $('#bedrock-status-tab .status-item-value.motd').text(data.bedrock_motd);
                   } else {
                       $('#bedrock-status-tab .edition-status-details').append(`
                           <div class="status-item">
                               <span class="status-item-label">MOTD:</span>
                               <span class="status-item-value motd">${data.bedrock_motd}</span>
                           </div>
                       `);
                   }
               }
           } else {
               $('#bedrock-status-tab .edition-status-details').hide();
               $('#bedrock-status-tab .edition-status-message').show();
           }
           
           // Update Bedrock player count in sidebar
           $('.player-edition-count:eq(1) .player-number').text(data.bedrock_player_count + '/' + data.bedrock_max_players);
       }
       
       // Update total player count
       const totalPlayers = data.java_player_count + data.bedrock_player_count;
       const totalMaxPlayers = data.java_max_players + data.bedrock_max_players;
       $('.player-edition-count.total .player-number').text(totalPlayers + '/' + totalMaxPlayers);
       $('.server-stat .stat-value:contains("Players")').text(totalPlayers);
   }
   
   // Show notification messages
   function showNotification(message, type = 'info') {
       // Remove any existing notifications
       $('.notification-toast').remove();
       
       const icon = {
           success: '<i class="fas fa-check-circle"></i>',
           error: '<i class="fas fa-exclamation-circle"></i>',
           warning: '<i class="fas fa-exclamation-triangle"></i>',
           info: '<i class="fas fa-info-circle"></i>'
       }[type] || '<i class="fas fa-info-circle"></i>';
       
       const $notification = $(`
           <div class="notification-toast notification-${type}">
               <div class="notification-icon">${icon}</div>
               <div class="notification-message">${message}</div>
               <button class="notification-close">×</button>
           </div>
       `);
       
       $('body').append($notification);
       setTimeout(() => $notification.addClass('show'), 100);
       
       const hideNotification = () => {
           $notification.removeClass('show');
           setTimeout(() => $notification.remove(), 300);
       };
       
       setTimeout(hideNotification, 5000);
       $notification.find('.notification-close').on('click', hideNotification);
   }
   
   // Load country flags
   loadCountryFlags();
   
   function loadCountryFlags() {
       $('.country-flag').each(function() {
           const countryCode = $(this).data('country')?.toLowerCase();
           if (countryCode) {
               $(this).attr('src', `https://flagcdn.com/w20/${countryCode}.png`);
           }
       });
   }
   
   // Smooth scroll to anchors
   $('a[href^="#"]').on('click', function(e) {
       const target = $(this.getAttribute('href'));
       if (target.length) {
           e.preventDefault();
           $('html, body').animate({
               scrollTop: target.offset().top - 80
           }, 500);
       }
   });
});
</script>
<style>
/* Minecraft Server Detail Styles */
:root {
  --primary-color: #4CAF50;
  --primary-dark: #388E3C;
  --primary-light: #C8E6C9;
  --secondary-color: #2196F3;
  --accent-color: #FF9800;
  --text-color: #333333;
  --text-light: #757575;
  --background-color: #FAFAFA;
  --card-color: #FFFFFF;
  --border-color: #E0E0E0;
  --success-color: #4CAF50;
  --error-color: #F44336;
  --warning-color: #FFC107;
  --info-color: #2196F3;
  --online-color: #4CAF50;
  --offline-color: #F44336;
  --java-color: #f3dd91;
  --java-text: #6d5a0d;
  --bedrock-color: #a8dbf2;
  --bedrock-text: #0e5d7e;
  --java_bedrock-color: #d4e8a5;
  --java_bedrock-text: #4b6310;
  --border-radius: 8px;
  --box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  --transition: all 0.3s ease;
}

.minecraft-server-detail {
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
  color: var(--text-color);
  line-height: 1.6;
  background-color: var(--background-color);
}

.container {
  max-width: 1280px;
  margin: 0 auto;
  padding: 0 20px;
}

/* Notification Messages */
.notification {
  padding: 15px 20px;
  border-radius: var(--border-radius);
  margin-bottom: 20px;
  display: flex;
  align-items: center;
  font-weight: 500;
  box-shadow: var(--box-shadow);
}

.notification.success {
  background-color: #E8F5E9;
  border-left: 4px solid var(--success-color);
  color: #2E7D32;
}

.notification.error {
  background-color: #FFEBEE;
  border-left: 4px solid var(--error-color);
  color: #C62828;
}

/* Server Hero Banner */
.server-hero {
  background-size: cover;
  background-position: center;
  color: white;
  padding: 60px 0 40px;
  position: relative;
}

.server-hero-content {
  display: flex;
  flex-direction: column;
  gap: 30px;
  position: relative;
  z-index: 2;
}

.server-badges {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  margin-bottom: -10px;
}

.server-badge {
  background-color: rgba(0,0,0,0.5);
  color: white;
  padding: 6px 15px;
  border-radius: 20px;
  font-size: 14px;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 5px;
}

.server-badge.featured {
  background-color: #FFD700;
  color: #333;
}

.server-badge.sponsored {
  background-color: #9C27B0;
}

.server-badge.premium {
  background-color: #FF9800;
}

.server-main-info {
  display: flex;
  align-items: center;
  gap: 25px;
}

.server-logo {
  width: 120px;
  height: 120px;
  border-radius: var(--border-radius);
  overflow: hidden;
  border: 4px solid white;
  box-shadow: 0 5px 15px rgba(0,0,0,0.2);
  flex-shrink: 0;
}

.server-thumbnail {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.server-placeholder {
  width: 100%;
  height: 100%;
  background-color: var(--primary-color);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 36px;
  font-weight: bold;
  color: white;
  text-transform: uppercase;
}

.server-title-info {
  flex: 1;
}

.server-title {
  font-size: 2.5em;
  margin: 0 0 15px;
  text-shadow: 0 2px 4px rgba(0,0,0,0.5);
  font-weight: 700;
  color: #ffffff;
}

.server-meta {
  display: flex;
  flex-wrap: wrap;
  gap: 15px;
  margin-bottom: 15px;
}

.server-edition {
  font-size: 14px;
  font-weight: 600;
  padding: 5px 12px;
  border-radius: 20px;
  display: inline-flex;
  align-items: center;
}

.server-edition.java {
  background-color: var(--java-color);
  color: var(--java-text);
}

.server-edition.bedrock {
  background-color: var(--bedrock-color);
  color: var(--bedrock-text);
}

.server-edition.java_bedrock {
  background-color: var(--java_bedrock-color);
  color: var(--java_bedrock-text);
}

.server-version, .server-country, .server-status {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 14px;
  padding: 5px 12px;
  border-radius: 20px;
  background-color: rgba(0, 0, 0, 0.3);
  color: white;
}

.country-flag {
  display: inline-block;
  width: 16px;
  height: 12px;
  border-radius: 2px;
  object-fit: cover;
}

.server-status.online {
  color: #8eff8e;
}

.server-status.offline {
  color: #ff8e8e;
}

.status-indicator {
  width: 8px;
  height: 8px;
  border-radius: 50%;
}

.server-status.online .status-indicator,
.status-indicator.online {
  background-color: var(--online-color);
}

.server-status.offline .status-indicator,
.status-indicator.offline {
  background-color: var(--offline-color);
}

.server-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.server-tag {
  background-color: rgba(255, 255, 255, 0.15);
  padding: 5px 12px;
  border-radius: 20px;
  font-size: 13px;
  color: white;
  text-decoration: none;
  transition: var(--transition);
}

.server-actions-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 20px;
  background-color: rgba(0,0,0,0.3);
  border-radius: var(--border-radius);
  padding: 20px;
  backdrop-filter: blur(5px);
}

.server-rating-box {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 5px;
}

.rating-stars {
  font-size: 20px;
  color: #FFD700;
  display: flex;
  align-items: center;
  gap: 5px;
}

.rating-stars .rating-value {
  margin-left: 8px;
  font-weight: 700;
  font-size: 18px;
  color: white;
}

.review-count {
  font-size: 14px;
  color: rgba(255,255,255,0.8);
}

.server-stats {
  display: flex;
  gap: 20px;
}

.server-stat {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 5px;
}

.server-stat i {
  font-size: 22px;
  color: var(--accent-color);
}

.stat-label {
  font-size: 12px;
  color: rgba(255,255,255,0.8);
}

.stat-value {
  font-size: 18px;
  font-weight: 700;
  color: white;
}

.server-primary-actions {
  display: flex;
  flex-wrap: wrap;
  gap: 15px;
  flex: 1;
  justify-content: flex-end;
  align-items: center;
}

.server-connection-info {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.server-ip-copy {
  background-color: rgba(0,0,0,0.2);
  border-radius: var(--border-radius);
  padding: 10px;
  cursor: pointer;
}

.ip-label {
  font-size: 12px;
  color: rgba(255,255,255,0.7);
  margin-bottom: 5px;
  font-weight: 600;
}

.ip-value {
  display: flex;
  align-items: center;
  gap: 10px;
}

.ip-text {
  font-family: 'Courier New', monospace;
  font-weight: 600;
  color: white;
}

.copy-ip-btn {
  background: rgba(255,255,255,0.1);
  border: none;
  color: white;
  padding: 5px 10px;
  border-radius: 4px;
  cursor: pointer;
  transition: var(--transition);
  display: flex;
  align-items: center;
  gap: 5px;
  font-size: 14px;
}

.copy-ip-btn:hover {
  background: rgba(255,255,255,0.2);
}

.bedrock-port {
  font-size: 12px;
  color: rgba(255,255,255,0.7);
  margin-top: 5px;
  padding-left: 8px;
}

.port-label {
  font-weight: 600;
}

.button-group {
  display: flex;
  gap: 10px;
}

.btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 12px 20px;
  border: none;
  border-radius: var(--border-radius);
  font-weight: 600;
  cursor: pointer;
  text-decoration: none;
  transition: var(--transition);
  font-size: 15px;
}

.btn-vote {
  background-color: var(--accent-color);
  color: white;
}

.btn-vote:hover {
  background-color: #FB8C00;
}

.btn-join {
  background-color: var(--primary-color);
  color: white;
}

.btn-join:hover {
  background-color: var(--primary-dark);
}

.btn-review {
  background-color: #FFC107;
  color: #333;
}

.btn-review:hover {
  background-color: #FFA000;
}

.btn-secondary {
  background-color: #f0f0f0;
  color: #555;
}

.btn-secondary:hover {
  background-color: #e0e0e0;
}

/* Main Content Section */
.server-content-section {
  padding: 40px 0;
}

.server-content-grid {
  display: grid;
  grid-template-columns: 1fr 350px;
  gap: 30px;
  margin-bottom: 40px;
}

.server-main-content {
  display: flex;
  flex-direction: column;
  gap: 30px;
}

.content-card {
  background-color: white;
  border-radius: var(--border-radius);
  box-shadow: var(--box-shadow);
  overflow: hidden;
}

.card-header {
  padding: 15px 20px;
  border-bottom: 1px solid var(--border-color);
  background-color: #f9f9f9;
}

.card-header h2,
.card-header h3 {
  margin: 0;
  font-size: 1.3em;
  display: flex;
  align-items: center;
  gap: 10px;
}

.card-header h2 i,
.card-header h3 i {
  color: var(--primary-color);
}

.card-body {
  padding: 20px;
}

/* Server Description */
.server-description-card .server-description {
  font-size: 16px;
  line-height: 1.7;
}

.server-description-card .server-description p:first-child {
  margin-top: 0;
}

.server-description-card .server-description p:last-child {
  margin-bottom: 0;
}

/* Server Status Details */
.server-status-details-card .server-status-tabs {
  display: flex;
  flex-direction: column;
}

.status-tab-headers {
  display: flex;
  border-bottom: 1px solid var(--border-color);
}

.status-tab-header {
  padding: 12px 20px;
  font-weight: 600;
  cursor: pointer;
  transition: var(--transition);
  border-bottom: 3px solid transparent;
}

.status-tab-header.active {
  color: var(--primary-color);
  border-bottom-color: var(--primary-color);
}

.status-tab-content {
  padding: 20px 0;
}

.status-tab-pane {
  display: none;
}

.status-tab-pane.active {
  display: block;
}

.edition-status {
  padding: 20px;
  border-radius: var(--border-radius);
  margin-bottom: 10px;
}

.edition-status.online {
  background-color: rgba(76, 175, 80, 0.1);
}

.edition-status.offline {
  background-color: rgba(244, 67, 54, 0.1);
}

.edition-status-header {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 15px;
}

.edition-status-header h3 {
  margin: 0;
  font-size: 18px;
}

.edition-status.online .edition-status-header {
  color: var(--primary-dark);
}

.edition-status.offline .edition-status-header {
  color: #C62828;
}

.edition-status-details {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 15px;
}

.status-item {
  display: flex;
  flex-direction: column;
  gap: 5px;
}

.status-item-label {
  font-size: 14px;
  color: var(--text-light);
}

.status-item-value {
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 5px;
}

.copy-btn {
  background: none;
  border: none;
  color: var(--primary-color);
  cursor: pointer;
  padding: 3px;
  font-size: 14px;
}

.edition-status-message {
  color: var(--text-light);
  font-style: italic;
}

/* Server Reviews */
.reviews-summary {
  display: flex;
  gap: 40px;
  margin-bottom: 30px;
  padding-bottom: 20px;
  border-bottom: 1px solid var(--border-color);
}

.overall-rating {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 5px;
}

.overall-rating .rating-value {
  font-size: 48px;
  font-weight: 700;
  line-height: 1;
}

.overall-rating .rating-stars {
  font-size: 20px;
  color: #FFD700;
}

.overall-rating .reviews-count {
  font-size: 14px;
  color: var(--text-light);
  margin-top: 5px;
}

.rating-breakdown {
  flex: 1;
}

.rating-bar {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 8px;
}

.rating-label {
  width: 50px;
  display: flex;
  align-items: center;
  gap: 5px;
  font-size: 14px;
}

.rating-label i {
  color: #FFD700;
}

.rating-progress {
  flex: 1;
  height: 8px;
  background-color: #f0f0f0;
  border-radius: 4px;
  overflow: hidden;
}

.progress-bar {
  height: 100%;
  background-color: #FFD700;
  border-radius: 4px;
}

.rating-count {
  width: 30px;
  text-align: right;
  font-size: 14px;
  color: var(--text-light);
}

.reviews-list {
  display: flex;
  flex-direction: column;
  gap: 20px;
  margin-bottom: 30px;
}

.review-item {
  background-color: #f9f9f9;
  border-radius: var(--border-radius);
  padding: 15px;
}

.review-header {
  display: flex;
  justify-content: space-between;
  margin-bottom: 10px;
}

.reviewer-name {
  font-weight: 600;
}

.review-date {
  font-size: 13px;
  color: var(--text-light);
}

.review-rating {
  color: #FFD700;
}

.review-content {
  font-size: 15px;
  line-height: 1.6;
}

.no-reviews {
  text-align: center;
  padding: 30px 0;
}

.no-reviews-icon {
  font-size: 48px;
  color: #ddd;
  margin-bottom: 15px;
}

.no-reviews h3 {
  margin: 0 0 10px;
}

.no-reviews p {
  margin: 0;
  color: var(--text-light);
}

.review-form-section {
  margin-top: 30px;
  padding-top: 30px;
  border-top: 1px solid var(--border-color);
}

.review-form-section h3 {
  margin-top: 0;
  margin-bottom: 20px;
}

.review-form {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.form-row {
  display: flex;
  gap: 20px;
}

.form-group {
  flex: 1;
  min-width: 0;
}

.form-group label {
  display: block;
  margin-bottom: 8px;
  font-weight: 600;
}

.required {
  color: var(--error-color);
}

.form-group input,
.form-group textarea {
  width: 100%;
  padding: 12px 15px;
  border: 1px solid var(--border-color);
  border-radius: var(--border-radius);
  font-family: inherit;
  font-size: 15px;
  transition: var(--transition);
}

.form-group input:focus,
.form-group textarea:focus {
  border-color: var(--primary-color);
  outline: none;
  box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1);
}

.field-note {
  margin-top: 5px;
  font-size: 13px;
  color: var(--text-light);
}

.rating-selector {
  display: flex;
  flex-direction: row-reverse;
  gap: 5px;
}

.rating-selector input {
  display: none;
}

.rating-selector label {
  cursor: pointer;
  font-size: 24px;
  color: #ddd;
}

.rating-selector input:checked ~ label,
.rating-selector label:hover,
.rating-selector label:hover ~ label {
  color: #FFD700;
}

.form-actions {
  margin-top: 10px;
}

.btn-submit {
  background-color: var(--primary-color);
  color: white;
}

.btn-submit:hover {
  background-color: var(--primary-dark);
}

/* Server Sidebar */
.server-sidebar {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.server-info-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.server-info-list li {
  display: flex;
  margin-bottom: 15px;
  padding-bottom: 15px;
  border-bottom: 1px solid var(--border-color);
}

.server-info-list li:last-child {
  margin-bottom: 0;
  padding-bottom: 0;
  border-bottom: none;
}

.info-label {
  width: 100px;
  font-weight: 600;
  color: var(--text-light);
}

.info-value {
  flex: 1;
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 5px;
}

.player-counts {
  display: flex;
  flex-direction: column;
  gap: 5px;
}

.player-edition-count {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.player-edition-count.total {
  border-top: 1px solid var(--border-color);
  padding-top: 5px;
  margin-top: 5px;
  font-weight: 600;
}

.edition-icon {
  display: flex;
  align-items: center;
  gap: 5px;
}

.player-number {
  font-weight: 600;
}

.edition-badge {
  display: inline-block;
  padding: 3px 10px;
  border-radius: 4px;
  font-size: 13px;
  font-weight: 600;
}

.edition-badge.java {
  background-color: var(--java-color);
  color: var(--java-text);
}

.edition-badge.bedrock {
  background-color: var(--bedrock-color);
  color: var(--bedrock-text);
}

.edition-badge.java_bedrock {
  background-color: var(--java_bedrock-color);
  color: var(--java_bedrock-text);
}

.server-types {
  display: flex;
  flex-wrap: wrap;
  gap: 5px;
}

.server-type-badge {
  display: inline-block;
  padding: 3px 10px;
  border-radius: 4px;
  font-size: 13px;
  background-color: #f0f0f0;
  color: var(--text-color);
  text-decoration: none;
  transition: var(--transition);
}

.server-type-badge:hover {
  background-color: #e0e0e0;
}

.country-display {
  display: flex;
  align-items: center;
  gap: 5px;
}

.server-links-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.server-links-list li {
  margin-bottom: 10px;
}

.server-links-list li:last-child {
  margin-bottom: 0;
}

.server-link {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 12px 15px;
  border-radius: var(--border-radius);
  text-decoration: none;
  color: var(--text-color);
  background-color: #f9f9f9;
  transition: var(--transition);
}

.server-link:hover {
  background-color: #f0f0f0;
}

.server-link span {
  flex: 1;
}

.website-link i:first-child {
  color: var(--secondary-color);
}

.discord-link i:first-child {
  color: #7289DA;
}

.vote-count {
  text-align: center;
  margin-bottom: 20px;
}

.vote-number {
  font-size: 42px;
  font-weight: 700;
  color: var(--accent-color);
  line-height: 1;
}

.vote-label {
  font-size: 14px;
  color: var(--text-light);
}

.btn-vote-large {
  width: 100%;
  background-color: var(--accent-color);
  color: white;
  padding: 15px;
  justify-content: center;
  font-size: 16px;
}

.btn-vote-large:hover {
  background-color: #FB8C00;
}

.vote-note {
  text-align: center;
  margin-top: 10px;
  font-size: 13px;
  color: var(--text-light);
}

.share-buttons {
  display: flex;
  justify-content: center;
  gap: 15px;
}

.share-button {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  text-decoration: none;
  transition: var(--transition);
}

.share-button:hover {
  transform: translateY(-3px);
}

.share-button.facebook {
  background-color: #3b5998;
}

.share-button.twitter {
  background-color: #1da1f2;
}

.share-button.reddit {
  background-color: #ff4500;
}

.share-button.email {
  background-color: #808080;
}

/* Join Instructions Card */
.server-join-card .join-section {
  margin-bottom: 25px;
  padding-bottom: 25px;
  border-bottom: 1px solid var(--border-color);
}

.server-join-card .join-section:last-child {
  margin-bottom: 0;
  padding-bottom: 0;
  border-bottom: none;
}

.server-join-card h4 {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-top: 0;
  margin-bottom: 15px;
  color: var(--primary-dark);
}

.server-join-card ol {
  margin: 0 0 20px;
  padding-left: 20px;
}

.server-join-card li {
  margin-bottom: 8px;
}

.server-join-card .join-direct {
  background-color: #f9f9f9;
  padding: 15px;
  border-radius: var(--border-radius);
  text-align: center;
}

.server-join-card .btn-join-direct {
  background-color: var(--primary-color);
  color: white;
  margin: 10px 0;
}

.server-join-card .btn-join-direct:hover {
  background-color: var(--primary-dark);
}

.server-join-card .join-note {
  font-size: 12px;
  color: var(--text-light);
  margin: 5px 0 0;
}

/* Related Servers */
.related-servers-section {
  margin-bottom: 40px;
}

.related-servers-section h2 {
  font-size: 1.5em;
  margin-bottom: 20px;
  display: flex;
  align-items: center;
  gap: 10px;
}

.related-servers-section h2 i {
  color: var(--primary-color);
}

.related-servers-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 20px;
}

.related-server-card {
  background-color: white;
  border-radius: var(--border-radius);
  overflow: hidden;
  box-shadow: var(--box-shadow);
  transition: var(--transition);
}

.related-server-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 20px rgba(0,0,0,0.1);
}

.related-server-link {
  text-decoration: none;
  color: inherit;
}

.related-server-banner {
  height: 180px;
  background-size: cover;
  background-position: center;
  position: relative;
  padding: 15px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.related-server-status {
  display: flex;
  align-items: center;
  gap: 5px;
  padding: 4px 10px;
  border-radius: 20px;
  background-color: rgba(0,0,0,0.5);
  font-size: 12px;
  color: white;
  align-self: flex-start;
}

.related-server-status.online .status-indicator {
  background-color: var(--online-color);
}

.related-server-status.offline .status-indicator {
  background-color: var(--offline-color);
}

.related-server-info {
  margin-top: auto;
}

.related-server-title {
  font-size: 18px;
  margin: 0 0 10px;
  color: white;
  text-shadow: 0 1px 3px rgba(0,0,0,0.6);
}

.related-server-meta {
  display: flex;
  gap: 10px;
}

.related-server-edition, .related-server-version {
  font-size: 12px;
  padding: 3px 8px;
  border-radius: 4px;
  display: flex;
  align-items: center;
  gap: 5px;
}

.related-server-edition.java {
  background-color: var(--java-color);
  color: var(--java-text);
}

.related-server-edition.bedrock {
  background-color: var(--bedrock-color);
  color: var(--bedrock-text);
}

.related-server-edition.java_bedrock {
  background-color: var(--java_bedrock-color);
  color: var(--java_bedrock-text);
}

.related-server-version {
  background-color: rgba(0,0,0,0.3);
  color: white;
}

.related-server-types {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-top: 10px;
}

.related-server-type {
  background-color: rgba(255, 255, 255, 0.15);
  padding: 3px 10px;
  border-radius: 20px;
  font-size: 12px;
  color: white;
}

.related-server-type.more {
  background-color: rgba(0, 0, 0, 0.2);
}

/* Back to Servers */
.back-to-servers {
  text-align: center;
  margin-bottom: 20px;
}

/* Responsive Adjustments */
@media (max-width: 1200px) {
  .server-actions-bar {
      flex-direction: column;
      align-items: flex-start;
  }
  
  .server-primary-actions {
      width: 100%;
      justify-content: space-between;
  }
  
  .server-connection-info {
      width: 100%;
  }
}

@media (max-width: 992px) {
  .server-content-grid {
      grid-template-columns: 1fr;
  }
  
  .server-title {
      font-size: 2em;
  }
  
  .reviews-summary {
      flex-direction: column;
      gap: 20px;
  }
  
  .edition-status-details {
      grid-template-columns: 1fr;
  }
}

@media (max-width: 768px) {
  .server-main-info {
      flex-direction: column;
      text-align: center;
  }
  
  .server-logo {
      margin: 0 auto;
  }
  
  .server-meta, .server-tags {
      justify-content: center;
  }
  
  .form-row {
      flex-direction: column;
      gap: 20px;
  }
  
  .server-primary-actions {
      flex-wrap: wrap;
  }
  
  .server-ip-copy {
      width: 100%;
  }
  
  .button-group {
      width: 100%;
      justify-content: space-between;
  }
}

@media (max-width: 576px) {
  .server-title {
      font-size: 1.8em;
  }
  
  .server-stats {
      width: 100%;
      justify-content: space-around;
  }
  
  .server-hero {
      padding: 40px 0 30px;
  }
  
  .button-group {
      flex-direction: column;
  }
  
  .button-group .btn {
      width: 100%;
  }
  
  .related-servers-grid {
      grid-template-columns: 1fr;
  }
}
</style>

<?php get_footer(); ?>