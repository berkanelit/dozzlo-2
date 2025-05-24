<?php
/*
Template Name: Minecraft Server List
*/

get_header();

// Initialize server configurations
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

// Get current filter values from URL parameters
$filter_edition = isset($_GET['edition']) ? sanitize_text_field($_GET['edition']) : '';
$filter_type = isset($_GET['type']) ? sanitize_text_field($_GET['type']) : '';
$filter_version = isset($_GET['version']) ? sanitize_text_field($_GET['version']) : '';
$filter_country = isset($_GET['country']) ? sanitize_text_field($_GET['country']) : '';
$filter_search = isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '';
$filter_online = isset($_GET['online']) ? filter_var($_GET['online'], FILTER_VALIDATE_BOOLEAN) : false;
$filter_premium = isset($_GET['premium']) ? filter_var($_GET['premium'], FILTER_VALIDATE_BOOLEAN) : false;
$sort_by = isset($_GET['sort']) ? sanitize_text_field($_GET['sort']) : 'rank';

// Get servers per page from settings
$servers_per_page = get_option('minecraft_server_list_servers_per_page', 20);
$current_page = max(1, get_query_var('paged'));

// Build the query args
$meta_query = array('relation' => 'AND');

// Only show approved servers
$meta_query[] = array(
    'key' => 'server_approved',
    'value' => 'approved',
    'compare' => '='
);

// Add edition filter - modified to handle multiple editions
if (!empty($filter_edition)) {
    // Updated to handle Java & Bedrock combined option
    if ($filter_edition === 'java_bedrock') {
        // Need both Java and Bedrock
        $meta_query[] = array(
            'relation' => 'AND',
            array(
                'key' => 'server_editions',
                'value' => 'java',
                'compare' => 'LIKE'
            ),
            array(
                'key' => 'server_editions',
                'value' => 'bedrock',
                'compare' => 'LIKE'
            )
        );
    } else {
        // Just one edition (Java or Bedrock)
        $meta_query[] = array(
            'key' => 'server_editions',
            'value' => $filter_edition,
            'compare' => 'LIKE'
        );
    }
}

// Add type filter
if (!empty($filter_type)) {
    $meta_query[] = array(
        'key' => 'server_type',
        'value' => $filter_type,
        'compare' => 'LIKE'
    );
}

// Add version filter
if (!empty($filter_version)) {
    $meta_query[] = array(
        'key' => 'server_version',
        'value' => $filter_version,
        'compare' => 'LIKE'
    );
}

// Add country filter
if (!empty($filter_country)) {
    $meta_query[] = array(
        'key' => 'server_country',
        'value' => $filter_country,
        'compare' => '='
    );
}

// Add premium filter
if ($filter_premium) {
    $meta_query[] = array(
        'key' => 'server_premium',
        'value' => 'yes',
        'compare' => '='
    );
}

// Add online filter (using cached status data)
if ($filter_online) {
    $meta_query[] = array(
        'key' => 'server_status',
        'value' => 'online',
        'compare' => '='
    );
}

// Add search filter
$search_query = '';
if (!empty($filter_search)) {
    $search_query = $filter_search;
}

// Sorting options
$orderby = 'meta_value_num';
$meta_key = 'server_rank';

switch ($sort_by) {
    case 'players':
        $meta_key = 'server_player_count';
        break;
    case 'votes':
        $meta_key = 'server_votes';
        break;
    case 'newest':
        $orderby = 'date';
        $meta_key = '';
        break;
    case 'rating':
        $meta_key = 'server_rating';
        break;
    case 'name':
        $orderby = 'title';
        $meta_key = '';
        break;
    default: // rank
        $meta_key = 'server_rank';
        break;
}

// Final query args
$query_args = array(
    'post_type' => 'minecraft_server',
    'posts_per_page' => $servers_per_page,
    'paged' => $current_page,
    'meta_query' => $meta_query,
    's' => $search_query
);

if (!empty($meta_key)) {
    $query_args['meta_key'] = $meta_key;
    $query_args['orderby'] = $orderby;
    $query_args['order'] = 'DESC';
} else {
    $query_args['orderby'] = $orderby;
    $query_args['order'] = ($orderby === 'title') ? 'ASC' : 'DESC';
}

// Run the query
$servers_query = new WP_Query($query_args);
$GLOBALS['servers_query'] = $servers_query; // Make it available for schema markup

// Check if we should show featured servers first
$show_featured_first = get_option('minecraft_server_list_featured_first', '1') === '1';

// Get featured servers
$featured_servers = array();
if ($show_featured_first && $current_page === 1) {
    $featured_args = array(
        'post_type' => 'minecraft_server',
        'posts_per_page' => 3,
        'meta_query' => array(
            'relation' => 'AND',
            array(
                'key' => 'server_approved',
                'value' => 'approved',
                'compare' => '='
            ),
            array(
                'key' => 'server_featured',
                'value' => 'yes',
                'compare' => '='
            )
        ),
        'orderby' => 'rand'
    );
    
    $featured_query = new WP_Query($featured_args);
    $featured_servers = $featured_query->posts;
}

// Get popular servers by player count
$popular_servers = array();
if ($current_page === 1) {
    $popular_args = array(
        'post_type' => 'minecraft_server',
        'posts_per_page' => 6,
        'meta_query' => array(
            'relation' => 'AND',
            array(
                'key' => 'server_approved',
                'value' => 'approved',
                'compare' => '='
            ),
            array(
                'key' => 'server_status',
                'value' => 'online',
                'compare' => '='
            )
        ),
        'meta_key' => 'server_player_count',
        'orderby' => 'meta_value_num',
        'order' => 'DESC'
    );
    
    $popular_query = new WP_Query($popular_args);
    $popular_servers = $popular_query->posts;
}

// Process server vote
if (isset($_POST['vote_for_server']) && isset($_POST['minecraft_server_vote_nonce']) && wp_verify_nonce($_POST['minecraft_server_vote_nonce'], 'vote_for_server')) {
    $server_id = isset($_POST['server_id']) ? intval($_POST['server_id']) : 0;
    
    if ($server_id > 0) {
        // Check if already voted using cookies
        $voted_cookie = 'mc_server_voted_' . $server_id;
        $can_vote = true;
        
        if (isset($_COOKIE[$voted_cookie])) {
            $can_vote = false;
            echo '<div class="notification error">You have already voted for this server today. Please come back tomorrow.</div>';
        } else {
            // Update vote count
            $votes = (int) get_post_meta($server_id, 'server_votes', true);
            $votes++;
            update_post_meta($server_id, 'server_votes', $votes);
            
            // Set cookie for 24 hours
            setcookie($voted_cookie, '1', time() + 86400, '/');
            
            echo '<div class="notification success">Thank you for your vote!</div>';
        }
    }
}

// Get total server count for stats
$total_servers = wp_count_posts('minecraft_server');
$approved_servers = $total_servers->publish;

// Get server counts by edition
$java_servers_count = 0;
$bedrock_servers_count = 0;
$java_bedrock_servers_count = 0;

// Count servers by edition
$java_servers_query = new WP_Query(array(
    'post_type' => 'minecraft_server',
    'posts_per_page' => -1,
    'meta_query' => array(
        array(
            'key' => 'server_editions',
            'value' => 'java',
            'compare' => 'LIKE'
        ),
        array(
            'key' => 'server_approved',
            'value' => 'approved',
            'compare' => '='
        )
    ),
    'fields' => 'ids'
));
$java_servers_count = $java_servers_query->found_posts;

$bedrock_servers_query = new WP_Query(array(
    'post_type' => 'minecraft_server',
    'posts_per_page' => -1,
    'meta_query' => array(
        array(
            'key' => 'server_editions',
            'value' => 'bedrock',
            'compare' => 'LIKE'
        ),
        array(
            'key' => 'server_approved',
            'value' => 'approved',
            'compare' => '='
        )
    ),
    'fields' => 'ids'
));
$bedrock_servers_count = $bedrock_servers_query->found_posts;

// Count servers that support both java and bedrock
$java_bedrock_servers_query = new WP_Query(array(
    'post_type' => 'minecraft_server',
    'posts_per_page' => -1,
    'meta_query' => array(
        'relation' => 'AND',
        array(
            'key' => 'server_editions',
            'value' => 'java',
            'compare' => 'LIKE'
        ),
        array(
            'key' => 'server_editions',
            'value' => 'bedrock',
            'compare' => 'LIKE'
        ),
        array(
            'key' => 'server_approved',
            'value' => 'approved',
            'compare' => '='
        )
    ),
    'fields' => 'ids'
));
$java_bedrock_servers_count = $java_bedrock_servers_query->found_posts;

// Get active servers count (use the cached data)
$active_servers_count = 0;
$active_servers_query = new WP_Query(array(
    'post_type' => 'minecraft_server',
    'posts_per_page' => 1,
    'meta_query' => array(
        array(
            'key' => 'server_approved',
            'value' => 'approved',
            'compare' => '='
        ),
        array(
            'key' => 'server_status',
            'value' => 'online',
            'compare' => '='
        )
    ),
    'fields' => 'ids'
));
$active_servers_count = $active_servers_query->found_posts;

// Get form data from transient if there was an error
$server_title = '';
$server_description = '';
$server_java_ip = '';
$server_bedrock_ip = '';
$server_port = '25565';
$server_bedrock_port = '19132';
$server_editions = array();
$server_version = '';
$server_country = '';
$server_type = array();
$server_website = '';
$server_discord = '';

$form_data = get_transient('minecraft_server_submission_data');
if ($form_data) {
    $server_title = isset($form_data['server_title']) ? $form_data['server_title'] : '';
    $server_description = isset($form_data['server_description']) ? $form_data['server_description'] : '';
    $server_java_ip = isset($form_data['server_java_ip']) ? $form_data['server_java_ip'] : '';
    $server_bedrock_ip = isset($form_data['server_bedrock_ip']) ? $form_data['server_bedrock_ip'] : '';
    $server_port = isset($form_data['server_port']) ? $form_data['server_port'] : '25565';
    $server_bedrock_port = isset($form_data['server_bedrock_port']) ? $form_data['server_bedrock_port'] : '19132';
    $server_editions = isset($form_data['server_editions']) ? $form_data['server_editions'] : array();
    $server_version = isset($form_data['server_version']) ? $form_data['server_version'] : '';
    $server_country = isset($form_data['server_country']) ? $form_data['server_country'] : '';
    $server_type = isset($form_data['server_type']) ? $form_data['server_type'] : array();
    $server_website = isset($form_data['server_website']) ? $form_data['server_website'] : '';
    $server_discord = isset($form_data['server_discord']) ? $form_data['server_discord'] : '';
    
    // Clear the transient
    delete_transient('minecraft_server_submission_data');
}
?>

<div class="minecraft-server-list">
    <!-- Hero Banner - Modified with transparent background -->
    <div class="server-list-hero">
        <div class="container">
            <div class="hero-content">
                <h1>Minecraft Server List</h1>
                <p class="hero-description">Find the best Minecraft servers for Java and Bedrock Edition. Browse our list of <?php echo esc_html($approved_servers); ?> servers, filter by type, and join a new gaming community today!</p>
                
                <!-- Stats badges kept intact -->
                <div class="server-stats-badges">
                    <div class="stat-badge">
                        <div class="stat-icon"><i class="fas fa-server"></i></div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo esc_html($approved_servers); ?></div>
                            <div class="stat-label">Total Servers</div>
                        </div>
                    </div>
                    
                    <div class="stat-badge">
                        <div class="stat-icon"><i class="fas fa-desktop"></i></div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo esc_html(number_format($java_servers_count)); ?></div>
                            <div class="stat-label">Java Servers</div>
                        </div>
                    </div>
                    
                    <div class="stat-badge">
                        <div class="stat-icon"><i class="fas fa-mobile-alt"></i></div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo esc_html(number_format($bedrock_servers_count)); ?></div>
                            <div class="stat-label">Bedrock Servers</div>
                        </div>
                    </div>
                    
                    <div class="stat-badge">
                        <div class="stat-icon"><i class="fas fa-users"></i></div>
                        <div class="stat-content">
                            <div class="stat-value"><?php echo esc_html(number_format($approved_servers * 50)); ?>+</div>
                            <div class="stat-label">Players</div>
                        </div>
                    </div>
                </div>
                
                <div class="hero-actions">
                    <a href="#add-server" class="btn btn-primary toggle-add-server">
                        <i class="fas fa-plus-circle"></i> Add Your Server
                    </a>
                    <a href="#filters" class="btn btn-secondary">
                        <i class="fas fa-filter"></i> Browse Servers
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Server Filters -->
    <div id="filters" class="server-filters-section">
        <div class="container">
            <div class="section-header">
                <h2><i class="fas fa-filter"></i> Find Your Perfect Server</h2>
                <p>Use the filters below to find the exact type of Minecraft server you're looking for.</p>
            </div>
            
            <div class="filters-card">
                <form method="get" action="<?php echo esc_url(get_permalink()); ?>" id="server-filter-form" class="server-filters">
                    <div class="filters-row">
                        <div class="filter-group">
                            <label for="edition">
                                <i class="fas fa-gamepad"></i> Edition
                            </label>
                            <select name="edition" id="edition" class="form-control">
                                <option value="">All Editions</option>
                                <?php foreach ($server_categories as $key => $label) : ?>
                                    <option value="<?php echo esc_attr($key); ?>" <?php selected($filter_edition, $key); ?>><?php echo esc_html($label); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <label for="type">
                                <i class="fas fa-tags"></i> Server Type
                            </label>
                            <select name="type" id="type" class="form-control">
                                <option value="">All Types</option>
                                <?php foreach ($server_types as $key => $label) : ?>
                                    <option value="<?php echo esc_attr($key); ?>" <?php selected($filter_type, $key); ?>><?php echo esc_html($label); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <label for="version">
                                <i class="fas fa-code-branch"></i> Version
                            </label>
                            <select name="version" id="version" class="form-control">
                                <option value="">All Versions</option>
                                <?php foreach ($server_versions as $key => $label) : ?>
                                    <option value="<?php echo esc_attr($key); ?>" <?php selected($filter_version, $key); ?>><?php echo esc_html($label); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <label for="country">
                                <i class="fas fa-globe"></i> Country
                            </label>
                            <select name="country" id="country" class="form-control">
                                <option value="">All Countries</option>
                                <?php foreach ($server_countries as $key => $label) : ?>
                                    <option value="<?php echo esc_attr($key); ?>" <?php selected($filter_country, $key); ?>><?php echo esc_html($label); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="filters-row">
                        <div class="filter-group search-group">
                            <label for="search">
                                <i class="fas fa-search"></i> Search
                            </label>
                            <input type="text" name="search" id="search" class="form-control" value="<?php echo esc_attr($filter_search); ?>" placeholder="Search by name, IP, or description...">
                        </div>
                        
                        <div class="filter-group">
                            <label for="sort">
                                <i class="fas fa-sort"></i> Sort By
                            </label>
                            <select name="sort" id="sort" class="form-control">
                                <option value="rank" <?php selected($sort_by, 'rank'); ?>>Rank</option>
                                <option value="players" <?php selected($sort_by, 'players'); ?>>Most Players</option>
                                <option value="votes" <?php selected($sort_by, 'votes'); ?>>Most Votes</option>
                                <option value="rating" <?php selected($sort_by, 'rating'); ?>>Highest Rating</option>
                                <option value="newest" <?php selected($sort_by, 'newest'); ?>>Newest</option>
                                <option value="name" <?php selected($sort_by, 'name'); ?>>Name (A-Z)</option>
                            </select>
                        </div>
                        
                        <div class="filter-group checkbox-filters">
                            <div class="checkbox-filter">
                                <input type="checkbox" name="online" id="online" value="1" <?php checked($filter_online); ?>>
                                <label for="online">
                                    <i class="fas fa-circle status-online"></i> Online Only
                                </label>
                            </div>
                            
                            <div class="checkbox-filter">
                                <input type="checkbox" name="premium" id="premium" value="1" <?php checked($filter_premium); ?>>
                                <label for="premium">
                                    <i class="fas fa-gem"></i> Premium Servers
                                </label>
                            </div>
                        </div>
                        
                        <div class="filter-actions">
                            <button type="submit" class="btn btn-apply">
                                <i class="fas fa-check"></i> Apply Filters
                            </button>
                            <a href="<?php echo esc_url(get_permalink()); ?>" class="btn btn-reset">
                                <i class="fas fa-times"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Server List -->
    <div class="server-list-section">
        <div class="container">
            <?php if ($servers_query->have_posts() || !empty($featured_servers) || !empty($popular_servers)) : ?>
                <!-- Active Filter Tags -->
                <?php if (!empty($filter_edition) || !empty($filter_type) || !empty($filter_version) || !empty($filter_country) || !empty($filter_search) || $filter_online || $filter_premium) : ?>
                    <div class="active-filters">
                        <span class="filters-label">Active Filters:</span>
                        
                        <?php if (!empty($filter_edition)) : ?>
                            <a href="<?php echo esc_url(add_query_arg(array('edition' => null))); ?>" class="filter-tag">
                                <span>Edition: <?php echo esc_html($server_categories[$filter_edition] ?? $filter_edition); ?></span>
                                <i class="fas fa-times"></i>
                            </a>
                        <?php endif; ?>
                        
                        <?php if (!empty($filter_type)) : ?>
                            <a href="<?php echo esc_url(add_query_arg(array('type' => null))); ?>" class="filter-tag">
                                <span>Type: <?php echo esc_html($server_types[$filter_type] ?? $filter_type); ?></span>
                                <i class="fas fa-times"></i>
                            </a>
                        <?php endif; ?>
                        
                        <?php if (!empty($filter_version)) : ?>
                            <a href="<?php echo esc_url(add_query_arg(array('version' => null))); ?>" class="filter-tag">
                                <span>Version: <?php echo esc_html($server_versions[$filter_version] ?? $filter_version); ?></span>
                                <i class="fas fa-times"></i>
                            </a>
                        <?php endif; ?>
                        
                        <?php if (!empty($filter_country)) : ?>
                            <a href="<?php echo esc_url(add_query_arg(array('country' => null))); ?>" class="filter-tag">
                                <span>Country: <?php echo esc_html($server_countries[$filter_country] ?? $filter_country); ?></span>
                                <i class="fas fa-times"></i>
                            </a>
                        <?php endif; ?>
                        
                        <?php if (!empty($filter_search)) : ?>
                            <a href="<?php echo esc_url(add_query_arg(array('search' => null))); ?>" class="filter-tag">
                                <span>Search: <?php echo esc_html($filter_search); ?></span>
                                <i class="fas fa-times"></i>
                            </a>
                        <?php endif; ?>
                        
                        <?php if ($filter_online) : ?>
                            <a href="<?php echo esc_url(add_query_arg(array('online' => null))); ?>" class="filter-tag">
                                <span>Online Only</span>
                                <i class="fas fa-times"></i>
                            </a>
                        <?php endif; ?>
                        
                        <?php if ($filter_premium) : ?>
                            <a href="<?php echo esc_url(add_query_arg(array('premium' => null))); ?>" class="filter-tag">
                                <span>Premium Servers</span>
                                <i class="fas fa-times"></i>
                            </a>
                        <?php endif; ?>
                        
                        <a href="<?php echo esc_url(get_permalink()); ?>" class="clear-all-filters">Clear All</a>
                    </div>
                <?php endif; ?>
                
                <!-- Featured Servers (first page only) -->
                <?php if (!empty($featured_servers) && $current_page === 1) : ?>
                    <div class="featured-servers">
                        <h3 class="list-section-title"><i class="fas fa-star"></i> Featured Servers</h3>
                        
                        <div class="featured-servers-grid">
                            <?php foreach ($featured_servers as $post) : setup_postdata($post); ?>
                                <?php
                                // Get server meta data
                                $server_id = get_the_ID();
                                
                                // Use the stored server status data
                                $server_status_data = get_post_meta($server_id, 'server_status_data', true);
                                
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
                                $server_votes = get_post_meta($server_id, 'server_votes', true) ?: 0;
                                $server_rating = get_post_meta($server_id, 'server_rating', true) ?: 0;
                                $server_review_count = get_post_meta($server_id, 'server_review_count', true) ?: 0;
                                $server_banner = get_post_meta($server_id, 'server_banner', true);
                                $server_status = get_post_meta($server_id, 'server_status', true) ?: 'offline';
                                
                                // Get server description for preview
                                $server_description = wp_trim_words(wp_strip_all_tags(get_the_content()), 20, '...');
                                
                                // Get specific status information from cached data
                                $java_status = get_post_meta($server_id, 'server_java_status', true) ?: 'offline';
                                $java_player_count = get_post_meta($server_id, 'server_java_player_count', true) ?: 0;
                                $java_max_players = get_post_meta($server_id, 'server_java_max_players', true) ?: 0;
                                $bedrock_status = get_post_meta($server_id, 'server_bedrock_status', true) ?: 'offline';
                                $bedrock_player_count = get_post_meta($server_id, 'server_bedrock_player_count', true) ?: 0;
                                $bedrock_max_players = get_post_meta($server_id, 'server_bedrock_max_players', true) ?: 0;
                                
                                // Calculate total players
                                $server_player_count = $java_player_count + $bedrock_player_count;
                                $server_max_players = $java_max_players + $bedrock_max_players;
                                
                                // Get up to 3 server types
                                $types_display = array();
                                $count = 0;
                                foreach ($server_types_array as $type) {
                                    if (isset($server_types[$type]) && $count < 3) {
                                        $types_display[] = $server_types[$type];
                                        $count++;
                                    }
                                }
                                
                                // Prepare banner image
                                $banner_url = '';
                                if (!empty($server_banner)) {
                                    $banner_url = wp_get_attachment_url($server_banner);
                                } else if (has_post_thumbnail()) {
                                    $banner_url = get_the_post_thumbnail_url(get_the_ID(), 'full');
                                } else {
                                    $banner_url = '';
                                }

                                // Get cached server status
                                $is_online = ($server_status === 'online');
                                ?>
                                <div class="featured-server-card">
									<a href="<?php the_permalink(); ?>" class="featured-banner-link">
    <div class="featured-banner" style="background-image: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.7)), url('<?php echo esc_url($banner_url); ?>');">
        <div class="featured-badge">
            <i class="fas fa-star"></i> Featured
        </div>
        
        <div class="server-edition <?php echo esc_attr($edition_badge); ?>">
            <?php echo esc_html($server_categories[$edition_badge] ?? ''); ?>
        </div>
        
        <div class="server-info-top">
            <div class="server-logo">
                <?php 
                if (has_post_thumbnail()) {
                    the_post_thumbnail('thumbnail', array('class' => 'server-thumbnail'));
                } else {
                    echo '<div class="server-placeholder">' . esc_html(substr(get_the_title(), 0, 2)) . '</div>';
                }
                ?>
            </div>
            
            <h3 class="server-title">
                <?php the_title(); ?>
            </h3>
        </div>
        
        <div class="server-details">
            <div class="server-meta-info">
                <div class="server-status <?php echo $is_online ? 'online' : 'offline'; ?>">
                    <div class="status-indicator"></div>
                    <div class="status-label"><?php echo $is_online ? 'Online' : 'Offline'; ?></div>
                </div>
                
                <?php if ($is_online): ?>
                <div class="player-count-box">
                    <?php if (in_array('java', $server_editions) && in_array('bedrock', $server_editions)): ?>
                        <div class="player-edition">
                            <span class="edition-icon"><i class="fas fa-desktop"></i></span>
                            <span class="current-players"><?php echo esc_html($java_player_count); ?></span>/<span class="max-players"><?php echo esc_html($java_max_players); ?></span>
                        </div>
                        <div class="player-edition">
                            <span class="edition-icon"><i class="fas fa-mobile-alt"></i></span>
                            <span class="current-players"><?php echo esc_html($bedrock_player_count); ?></span>/<span class="max-players"><?php echo esc_html($bedrock_max_players); ?></span>
                        </div>
                    <?php else: ?>
                        <div class="player-count">
                            <span class="current-players"><?php echo esc_html($server_player_count); ?></span>/<span class="max-players"><?php echo esc_html($server_max_players); ?></span> players
                        </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($server_country) && isset($server_countries[$server_country])) : ?>
                    <div class="server-country">
                        <img src="" data-country="<?php echo esc_attr($server_country); ?>" alt="<?php echo esc_attr($server_countries[$server_country]); ?>" class="country-flag">
                        <span><?php echo esc_html($server_countries[$server_country]); ?></span>
                    </div>
                <?php endif; ?>
                
                <div class="server-version">
                    <i class="fas fa-code-branch"></i>
                    <span><?php echo esc_html($server_version); ?></span>
                </div>
            </div>
            
            <div class="server-rating-display">
                <div class="rating-stars">
                    <?php 
                    for ($i = 1; $i <= 5; $i++) {
                        if ($i <= floor($server_rating)) {
                            echo '<i class="fas fa-star"></i>';
                        } elseif ($i - 0.5 <= $server_rating) {
                            echo '<i class="fas fa-star-half-alt"></i>';
                        } else {
                            echo '<i class="far fa-star"></i>';
                        }
                    }
                    ?>
                    <span class="rating-value"><?php echo number_format($server_rating, 1); ?></span>
                </div>
            </div>
                                      
            <div class="server-tags">
                <?php foreach ($types_display as $type_label) : ?>
                    <span class="server-tag"><?php echo esc_html($type_label); ?></span>
                <?php endforeach; ?>
                
                <?php if (count($server_types_array) > count($types_display)) : ?>
                    <span class="server-tag more">+<?php echo (count($server_types_array) - count($types_display)); ?> more</span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</a>

<div class="featured-server-footer">
    <div class="server-address">
        <?php if (in_array('java', $server_editions)) : ?>
            <div class="ip-container">
                <span class="ip-label">Java IP:</span>
                <div class="ip-copy" data-clipboard="<?php echo esc_attr($server_java_ip . ($server_port != '25565' ? ':' . $server_port : '')); ?>">
                    <span class="ip-text"><?php echo esc_html($server_java_ip . ($server_port != '25565' ? ':' . $server_port : '')); ?></span>
                    <button type="button" class="copy-ip-btn" aria-label="Copy IP">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
            </div>
        <?php endif; ?>
        
        <?php if (in_array('bedrock', $server_editions)) : ?>
            <div class="ip-container">
                <span class="ip-label">Bedrock IP:</span>
                <div class="ip-copy" data-clipboard="<?php echo esc_attr($server_bedrock_ip); ?>">
                    <span class="ip-text"><?php echo esc_html($server_bedrock_ip); ?></span>
                    <button type="button" class="copy-ip-btn" aria-label="Copy IP">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
                <div class="bedrock-port">
                    <span class="port-label">Port: </span>
                    <span class="port-value"><?php echo esc_html($server_bedrock_port); ?></span>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="server-stats">
        <div class="stat-votes" title="<?php echo esc_attr($server_votes); ?> votes">
            <i class="fas fa-thumbs-up"></i>
            <span><?php echo esc_html($server_votes); ?></span>
        </div>
        
        <?php if ($server_rating > 0) : ?>
            <div class="stat-rating" title="<?php echo esc_attr(number_format($server_rating, 1)); ?> rating">
                <i class="fas fa-star"></i>
                <span><?php echo esc_html(number_format($server_rating, 1)); ?></span>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="server-actions">
        <form method="post" action="#server-<?php echo esc_attr($server_id); ?>" class="vote-form">
            <?php wp_nonce_field('vote_for_server', 'minecraft_server_vote_nonce'); ?>
            <input type="hidden" name="server_id" value="<?php echo esc_attr($server_id); ?>">
            <button type="submit" name="vote_for_server" class="btn btn-vote">
                <i class="fas fa-thumbs-up"></i>
            </button>
        </form>
        
        <a href="<?php the_permalink(); ?>" class="btn btn-view">
            <i class="fas fa-external-link-alt"></i> View
        </a>
        
        <?php if (in_array('bedrock', $server_editions)) : ?>
            <a href="minecraft://connect/<?php echo esc_attr($server_bedrock_ip . ':' . $server_bedrock_port); ?>" class="btn btn-join">
                <i class="fas fa-play"></i> Join
            </a>
        <?php endif; ?>
    </div>
</div>
                                </div>
                            <?php endforeach; ?>
                            <?php wp_reset_postdata(); // Reset featured servers ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Popular Servers by Player Count (NEW SECTION) -->
                <?php if (!empty($popular_servers) && $current_page === 1) : ?>
                    <div class="popular-servers">
                        <h3 class="list-section-title"><i class="fas fa-users"></i> Most Popular Servers</h3>
                        
                        <div class="popular-servers-grid">
                            <?php foreach ($popular_servers as $post) : setup_postdata($post); ?>
                                <?php
                                // Get server meta data
                                $server_id = get_the_ID();
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
                                $server_votes = get_post_meta($server_id, 'server_votes', true) ?: 0;
                                $server_rating = get_post_meta($server_id, 'server_rating', true) ?: 0;
                                $server_review_count = get_post_meta($server_id, 'server_review_count', true) ?: 0;
                                $server_banner = get_post_meta($server_id, 'server_banner', true);
                                $server_status = get_post_meta($server_id, 'server_status', true) ?: 'offline';
                                
                                // Get server description for preview
                                $server_description = wp_trim_words(wp_strip_all_tags(get_the_content()), 15, '...');
                                
                                // Edition-specific status information
                                $java_status = get_post_meta($server_id, 'server_java_status', true) ?: 'offline';
                                $java_player_count = get_post_meta($server_id, 'server_java_player_count', true) ?: 0;
                                $java_max_players = get_post_meta($server_id, 'server_java_max_players', true) ?: 0;
                                $bedrock_status = get_post_meta($server_id, 'server_bedrock_status', true) ?: 'offline';
                                $bedrock_player_count = get_post_meta($server_id, 'server_bedrock_player_count', true) ?: 0;
                                $bedrock_max_players = get_post_meta($server_id, 'server_bedrock_max_players', true) ?: 0;
                                
                                // Calculate total players
                                $server_player_count = $java_player_count + $bedrock_player_count;
                                $server_max_players = $java_max_players + $bedrock_max_players;
                                
                                // Get up to 2 server types for display
                                $types_display = array();
                                $count = 0;
                                foreach ($server_types_array as $type) {
                                    if (isset($server_types[$type]) && $count < 2) {
                                        $types_display[] = $server_types[$type];
                                        $count++;
                                    }
                                }
                                
                                // Prepare banner image
                                $banner_url = '';
                                if (!empty($server_banner)) {
                                    $banner_url = wp_get_attachment_url($server_banner);
                                } else if (has_post_thumbnail()) {
                                    $banner_url = get_the_post_thumbnail_url(get_the_ID(), 'full');
                                } else {
                                    $banner_url = 'https://via.placeholder.com/600x200.png?text=No+Banner';
                                }

                                // Get cached server status
                                $is_online = ($server_status === 'online');
                                ?>
                                <div class="popular-server-card">
                                    <a href="<?php the_permalink(); ?>" class="popular-server-link">
                                        <div class="popular-server-banner" style="background-image: linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.7)), url('<?php echo esc_url($banner_url); ?>');">
                                            <div class="popular-badge">
                                                <i class="fas fa-fire"></i> <?php echo esc_html($server_player_count); ?> Players
                                            </div>
                                            
                                            <div class="server-edition <?php echo esc_attr($edition_badge); ?>">
                                                <?php echo esc_html($server_categories[$edition_badge] ?? ''); ?>
                                            </div>
                                            
                                            <div class="server-header-content">
                                                <div class="server-logo">
                                                    <?php 
                                                    if (has_post_thumbnail()) {
                                                        the_post_thumbnail('thumbnail', array('class' => 'server-thumbnail'));
                                                    } else {
                                                        echo '<div class="server-placeholder">' . esc_html(substr(get_the_title(), 0, 2)) . '</div>';
                                                    }
                                                    ?>
                                                </div>
                                                
                                                <div class="server-title-area">
                                                    <h3 class="server-title">
                                                        <?php the_title(); ?>
                                                    </h3>
                                                    
                                                    <div class="server-meta-info">
                                                        <div class="server-status <?php echo $is_online ? 'online' : 'offline'; ?>">
                                                            <div class="status-indicator"></div>
                                                            <div class="status-label"><?php echo $is_online ? 'Online' : 'Offline'; ?></div>
                                                        </div>
                                                        
                                                        <div class="server-version">
                                                            <i class="fas fa-code-branch"></i>
                                                            <span><?php echo esc_html($server_version); ?></span>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="server-rating-display">
                                                        <div class="rating-stars">
                                                            <?php 
                                                            for ($i = 1; $i <= 5; $i++) {
                                                                if ($i <= floor($server_rating)) {
                                                                    echo '<i class="fas fa-star"></i>';
                                                                } elseif ($i - 0.5 <= $server_rating) {
                                                                    echo '<i class="fas fa-star-half-alt"></i>';
                                                                } else {
                                                                    echo '<i class="far fa-star"></i>';
                                                                }
                                                            }
                                                            ?>
                                                            <span class="rating-value"><?php echo number_format($server_rating, 1); ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                            
                                            <div class="server-footer-content">
                                                <div class="server-tags-container">
                                                    <?php foreach ($types_display as $type_label) : ?>
                                                        <span class="server-tag"><?php echo esc_html($type_label); ?></span>
                                                    <?php endforeach; ?>
                                                    
                                                    <?php if (count($server_types_array) > count($types_display)) : ?>
                                                        <span class="server-tag more">+<?php echo (count($server_types_array) - count($types_display)); ?> more</span>
                                                    <?php endif; ?>
                                                </div>
                                                
                                                <div class="server-quick-actions">
                                                    <?php if (in_array('java', $server_editions)) : ?>
                                                        <div class="server-ip-copy" data-clipboard="<?php echo esc_attr($server_java_ip . ($server_port != '25565' ? ':' . $server_port : '')); ?>">
                                                            <i class="fas fa-desktop"></i>
                                                            <i class="fas fa-copy"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                    
                                                    <?php if (in_array('bedrock', $server_editions)) : ?>
                                                        <div class="server-ip-copy" data-clipboard="<?php echo esc_attr($server_bedrock_ip); ?>">
                                                            <i class="fas fa-mobile-alt"></i>
                                                            <i class="fas fa-copy"></i>
                                                        </div>
                                                        
                                                        <a href="minecraft://connect/<?php echo esc_attr($server_bedrock_ip . ':' . $server_bedrock_port); ?>" class="join-button">
                                                            <i class="fas fa-play"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                            <?php wp_reset_postdata(); // Reset popular servers ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="servers-list">
                    <h3 class="list-section-title">
                        <i class="fas fa-server"></i> All Servers
                        <span class="server-count">(<?php echo esc_html($servers_query->found_posts); ?> servers found)</span>
                    </h3>
                    
                    <div class="servers-grid">
                        <?php while ($servers_query->have_posts()) : $servers_query->the_post(); ?>
                            <?php
                            // Get server meta data
                            $server_id = get_the_ID();
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
                            $server_featured = get_post_meta($server_id, 'server_featured', true) == 'yes';
                            $server_sponsored = get_post_meta($server_id, 'server_sponsored', true) == 'yes';
                            $server_premium = get_post_meta($server_id, 'server_premium', true) == 'yes';
                            $server_banner = get_post_meta($server_id, 'server_banner', true);
                            $server_status = get_post_meta($server_id, 'server_status', true) ?: 'offline';
                            
                            // Get server description for preview
                            $server_description = wp_trim_words(wp_strip_all_tags(get_the_content()), 15, '...');
                            
                            // Edition-specific status information
                            $java_status = get_post_meta($server_id, 'server_java_status', true) ?: 'offline';
                            $java_player_count = get_post_meta($server_id, 'server_java_player_count', true) ?: 0;
                            $java_max_players = get_post_meta($server_id, 'server_java_max_players', true) ?: 0;
                           $bedrock_status = get_post_meta($server_id, 'server_bedrock_status', true) ?: 'offline';
                           $bedrock_player_count = get_post_meta($server_id, 'server_bedrock_player_count', true) ?: 0;
                           $bedrock_max_players = get_post_meta($server_id, 'server_bedrock_max_players', true) ?: 0;
                           
                           // Calculate total players
                           $server_player_count = $java_player_count + $bedrock_player_count;
                           $server_max_players = $java_max_players + $bedrock_max_players;
                           
                           // Get up to 3 server types
                           $types_display = array();
                           $count = 0;
                           foreach ($server_types_array as $type) {
                               if (isset($server_types[$type]) && $count < 3) {
                                   $types_display[] = $server_types[$type];
                                   $count++;
                               }
                           }
                           
                           // Prepare banner image
                           $banner_url = '';
                           if (!empty($server_banner)) {
                               $banner_url = wp_get_attachment_url($server_banner);
                           } else if (has_post_thumbnail()) {
                               $banner_url = get_the_post_thumbnail_url(get_the_ID(), 'full');
                           } else {
                               $banner_url = 'https://via.placeholder.com/600x200.png?text=No+Banner'; 
                           }

                           // Get cached server status
                           $is_online = ($server_status === 'online');
                           
                           // Get last checked time
                           $last_checked = get_post_meta($server_id, 'server_last_checked', true);
                           $last_checked_display = empty($last_checked) ? 'Never' : human_time_diff(strtotime($last_checked), current_time('timestamp')) . ' ago';
                           ?>
                           
                           <!-- Server Card -->
                           <div class="server-card" data-id="<?php echo esc_attr($server_id); ?>">
                               <div class="server-card-banner">
                                   <a href="<?php the_permalink(); ?>" class="server-banner-link" style="background-image: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.7)), url('<?php echo esc_url($banner_url); ?>'); height: 220px; min-height: 220px;">
                                       <?php if ($server_featured || $server_sponsored) : ?>
                                           <div class="server-badges">
                                               <?php if ($server_featured) : ?>
                                                   <div class="server-badge featured" title="Featured Server">
                                                       <i class="fas fa-star"></i>
                                                   </div>
                                               <?php endif; ?>
                                               
                                               <?php if ($server_sponsored) : ?>
                                                   <div class="server-badge sponsored" title="Sponsored Server">
                                                       <i class="fas fa-ad"></i>
                                                   </div>
                                               <?php endif; ?>
                                           </div>
                                       <?php endif; ?>
                                       
                                       <div class="server-rank-badge">
                                           <span class="rank-number"><?php echo esc_html($server_rank); ?></span>
                                       </div>
                                       
                                       <div class="server-header-info">
                                           <div class="server-logo">
                                               <?php 
                                               if (has_post_thumbnail()) {
                                                   the_post_thumbnail('thumbnail', array('class' => 'server-thumbnail'));
                                               } else {
                                                   echo '<div class="server-placeholder">' . esc_html(substr(get_the_title(), 0, 2)) . '</div>';
                                               }
                                               ?>
                                           </div>
                                           
                                           <div class="server-edition <?php echo esc_attr($edition_badge); ?>">
                                               <?php echo esc_html($server_categories[$edition_badge] ?? ''); ?>
                                           </div>
                                       </div>
                                       
                                       <h3 class="server-title">
                                           <?php the_title(); ?>
                                       </h3>
                                       
                                       <div class="server-meta">
                                           <div class="server-status <?php echo $is_online ? 'online' : 'offline'; ?>">
                                               <div class="status-indicator"></div>
                                               <span class="status-text"><?php echo $is_online ? 'Online' : 'Offline'; ?></span>
                                               <span class="status-last-checked" title="Last checked: <?php echo esc_attr($last_checked_display); ?>">
                                                   <i class="fas fa-history"></i>
                                               </span>
                                           </div>
                                           
                                           <?php if ($is_online): ?>
                                           <div class="player-count-box">
                                               <?php if (in_array('java', $server_editions) && in_array('bedrock', $server_editions)): ?>
                                                   <div class="player-edition">
                                                       <span class="edition-icon"><i class="fas fa-desktop"></i></span>
                                                       <span class="current-players"><?php echo esc_html($java_player_count); ?></span>/<span class="max-players"><?php echo esc_html($java_max_players); ?></span>
                                                   </div>
                                                   <div class="player-edition">
                                                       <span class="edition-icon"><i class="fas fa-mobile-alt"></i></span>
                                                       <span class="current-players"><?php echo esc_html($bedrock_player_count); ?></span>/<span class="max-players"><?php echo esc_html($bedrock_max_players); ?></span>
                                                   </div>
                                               <?php else: ?>
                                                   <div class="player-count">
                                                       (<span class="current-players"><?php echo esc_html($server_player_count); ?></span>/<span class="max-players"><?php echo esc_html($server_max_players); ?></span>)
                                                   </div>
                                               <?php endif; ?>
                                           </div>
                                           <?php endif; ?>
                                           
                                           <?php if (!empty($server_version)) : ?>
                                               <div class="server-version">
                                                   <i class="fas fa-code-branch"></i>
                                                   <span><?php echo esc_html($server_version); ?></span>
                                              </div>
                                          <?php endif; ?>
                                          
                                          <?php if (!empty($server_country) && isset($server_countries[$server_country])) : ?>
                                              <div class="server-country">
                                                  <img src="" data-country="<?php echo esc_attr($server_country); ?>" alt="<?php echo esc_attr($server_countries[$server_country]); ?>" class="country-flag">
                                                  <span><?php echo esc_html($server_countries[$server_country]); ?></span>
                                              </div>
                                          <?php endif; ?>
                                      </div>
                                      
                                      <div class="server-rating-display">
                                          <div class="rating-stars">
                                              <?php 
                                              for ($i = 1; $i <= 5; $i++) {
                                                  if ($i <= floor($server_rating)) {
                                                      echo '<i class="fas fa-star"></i>';
                                                  } elseif ($i - 0.5 <= $server_rating) {
                                                      echo '<i class="fas fa-star-half-alt"></i>';
                                                  } else {
                                                      echo '<i class="far fa-star"></i>';
                                                  }
                                              }
                                              ?>
                                              <span class="rating-value"><?php echo number_format($server_rating, 1); ?></span>
                                          </div>
                                      </div>          
                                      
                                      <div class="server-tags">
                                          <?php foreach ($types_display as $type_label) : ?>
                                              <a href="<?php echo esc_url(add_query_arg('type', array_search($type_label, $server_types), get_permalink())); ?>" class="server-tag"><?php echo esc_html($type_label); ?></a>
                                          <?php endforeach; ?>
                                      
                                          <?php if (count($server_types_array) > count($types_display)) : ?>
                                              <span class="server-tag more">+<?php echo (count($server_types_array) - count($types_display)); ?> more</span>
                                          <?php endif; ?>
                                      
                                          <?php if ($server_premium) : ?>
                                              <span class="server-tag premium">
                                                  <i class="fas fa-gem"></i> Premium
                                              </span>
                                          <?php endif; ?>
                                      </div>
                                  </a>
                              </div>
                              
                              <div class="server-card-content">
                                  <div class="server-tags">
                                      <?php foreach ($types_display as $type_label) : ?>
                                          <a href="<?php echo esc_url(add_query_arg('type', array_search($type_label, $server_types), get_permalink())); ?>" class="server-tag"><?php echo esc_html($type_label); ?></a>
                                      <?php endforeach; ?>
                                      
                                      <?php if (count($server_types_array) > count($types_display)) : ?>
                                          <span class="server-tag more">+<?php echo (count($server_types_array) - count($types_display)); ?> more</span>
                                      <?php endif; ?>
                                      
                                      <?php if ($server_premium) : ?>
                                          <span class="server-tag premium">
                                              <i class="fas fa-gem"></i> Premium
                                          </span>
                                      <?php endif; ?>
                                  </div>
                                  
                                  <div class="server-card-footer">
                                      <div class="server-stats">
                                          <div class="stat-votes" title="<?php echo esc_attr($server_votes); ?> votes">
                                              <i class="fas fa-thumbs-up"></i>
                                              <span><?php echo esc_html($server_votes); ?></span>
                                          </div>
                                          
                                          <?php if ($server_rating > 0) : ?>
                                              <div class="stat-rating" title="<?php echo esc_attr(number_format($server_rating, 1)); ?> rating">
                                                  <i class="fas fa-star"></i>
                                                  <span><?php echo esc_html(number_format($server_rating, 1)); ?></span>
                                              </div>
                                          <?php endif; ?>
                                      </div>
                                      
                                      <div class="server-ips">
                                          <?php if (in_array('java', $server_editions)) : ?>
                                              <div class="server-ip-copy" data-clipboard="<?php echo esc_attr($server_java_ip . ($server_port != '25565' ? ':' . $server_port : '')); ?>">
                                                  <span class="ip-label">Java:</span>
                                                  <span class="ip-text"><?php echo esc_html($server_java_ip . ($server_port != '25565' ? ':' . $server_port : '')); ?></span>
                                                  <button type="button" class="copy-ip-btn">
                                                      <i class="fas fa-copy"></i>
                                                  </button>
                                              </div>
                                          <?php endif; ?>
                                          
                                          <?php if (in_array('bedrock', $server_editions)) : ?>
                                              <div class="server-ip-copy" data-clipboard="<?php echo esc_attr($server_bedrock_ip); ?>">
                                                  <span class="ip-label">Bedrock:</span>
                                                  <span class="ip-text"><?php echo esc_html($server_bedrock_ip); ?></span>
                                                  <button type="button" class="copy-ip-btn">
                                                      <i class="fas fa-copy"></i>
                                                  </button>
                                              </div>
                                              <div class="bedrock-port">
                                                  <span class="port-label">Port: </span>
                                                  <span class="port-value"><?php echo esc_html($server_bedrock_port); ?></span>
                                              </div>
                                          <?php endif; ?>
                                      </div>
                                      
                                      <div class="server-actions">
                                          <form method="post" action="#server-<?php echo esc_attr($server_id); ?>" class="vote-form">
                                              <?php wp_nonce_field('vote_for_server', 'minecraft_server_vote_nonce'); ?>
                                              <input type="hidden" name="server_id" value="<?php echo esc_attr($server_id); ?>">
                                              <button type="submit" name="vote_for_server" class="btn btn-vote">
                                                  <i class="fas fa-thumbs-up"></i>
                                              </button>
                                          </form>
                                          
                                          <a href="<?php the_permalink(); ?>" class="btn btn-view">
                                              <i class="fas fa-external-link-alt"></i>
                                          </a>
                                          
                                          <?php if (in_array('bedrock', $server_editions)) : ?>
                                              <a href="minecraft://connect/<?php echo esc_attr($server_bedrock_ip . ':' . $server_bedrock_port); ?>" class="btn btn-join">
                                                  <i class="fas fa-play"></i>
                                              </a>
                                          <?php endif; ?>
                                          
                                          <?php if ($server_discord) : ?>
                                              <a href="<?php echo esc_url($server_discord); ?>" target="_blank" rel="nofollow noopener" class="btn btn-discord">
                                                  <i class="fab fa-discord"></i>
                                              </a>
                                          <?php endif; ?>
                                      </div>
                                  </div>
                              </div>
                          </div>
                      <?php endwhile; ?>
                  </div>
                  
                  <?php
                  // Pagination
                  $big = 999999999;
                  echo '<div class="server-pagination">';
                  echo paginate_links(array(
                      'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
                      'format' => '?paged=%#%',
                      'current' => max(1, $current_page),
                      'total' => $servers_query->max_num_pages,
                      'prev_text' => '<i class="fas fa-chevron-left"></i> Previous',
                      'next_text' => 'Next <i class="fas fa-chevron-right"></i>',
                      'type' => 'list',
                      'end_size' => 1,
                      'mid_size' => 2
                  ));
                  echo '</div>';
                  
                  wp_reset_postdata();
                  ?>
              </div>
          <?php else : ?>
              <div class="no-servers-found">
                  <div class="no-results-icon">
                      <i class="fas fa-search"></i>
                  </div>
                  <h3>No Servers Found</h3>
                  <p>No servers match your current filters. Try adjusting your search criteria or <a href="<?php echo esc_url(get_permalink()); ?>">browse all servers</a>.</p>
                  
                  <div class="no-results-actions">
                      <a href="<?php echo esc_url(get_permalink()); ?>" class="btn btn-secondary">
                          <i class="fas fa-undo"></i> Reset Filters
                      </a>
                      <a href="#add-server" class="btn btn-primary toggle-add-server">
                          <i class="fas fa-plus-circle"></i> Add Your Server
                      </a>
                  </div>
              </div>
          <?php endif; ?>
      </div>
  </div>

  <!-- Add Server Form -->
  <?php
  // Check if submissions are enabled
  $submissions_enabled = get_option('minecraft_server_list_submissions_enabled', '1');

  if ($submissions_enabled === '1') :
  ?>
  <div id="add-server" class="add-server-section" style="display: none;">
      <div class="container">
          <div class="section-header">
              <h2><i class="fas fa-plus-circle"></i> Add Your Minecraft Server</h2>
              <p>List your server in our directory to reach more players and grow your community.</p>
          </div>
          
          <div class="add-server-content">
              <div class="add-server-benefits">
                  <h3>Why List Your Server With Us?</h3>
                  <ul class="benefits-list">
                      <li>
                          <i class="fas fa-users"></i>
                          <div class="benefit-content">
                              <h4>Reach More Players</h4>
                              <p>Get discovered by thousands of potential players looking for new Minecraft servers to join.</p>
                          </div>
                      </li>
                      <li>
                          <i class="fas fa-chart-line"></i>
                          <div class="benefit-content">
                              <h4>Track Performance</h4>
                              <p>Monitor your server's votes, status, and ranking to see how you're performing.</p>
                          </div>
                      </li>
                      <li>
                          <i class="fas fa-search"></i>
                          <div class="benefit-content">
                              <h4>Improve Visibility</h4>
                              <p>Make your server easily discoverable through our robust search and filtering system.</p>
                          </div>
                      </li>
                      <li>
                          <i class="fas fa-star"></i>
                          <div class="benefit-content">
                              <h4>Get Player Feedback</h4>
                              <p>Receive reviews and ratings from players to help improve your server.</p>
                          </div>
                      </li>
                  </ul>
              </div>
              
              <div class="server-submission-form">
                  <form method="post" action="#add-server" enctype="multipart/form-data" id="minecraft-server-form">
                      <?php wp_nonce_field('submit_minecraft_server', 'minecraft_server_submission_nonce'); ?>
                      
                      <!-- Display any form errors -->
                      <?php if (isset($_GET['submission_error']) && $_GET['submission_error'] === '1') : 
                          $error_message = get_transient('minecraft_server_submission_errors');
                          if ($error_message) {
                              echo $error_message;
                              delete_transient('minecraft_server_submission_errors');
                          }
                      endif; ?>
                      
                      <!-- Form loader for submission -->
                      <div class="form-loader">
                          <div class="loader"></div>
                      </div>
                      
                      <div class="form-section">
                          <h3>Basic Information</h3>
                          
                          <div class="form-group">
                              <label for="server_title">Server Name <span class="required">*</span></label>
                              <input type="text" name="server_title" id="server_title" value="<?php echo esc_attr($server_title); ?>" required>
                          </div>
                          
                          <div class="form-group">
                              <label for="server_description">Server Description <span class="required">*</span></label>
                              <textarea name="server_description" id="server_description" rows="5" required><?php echo esc_textarea($server_description); ?></textarea>
                              <div class="field-note">Describe your server, including unique features, gameplay, and community aspects.</div>
                          </div>
                      </div>
                      
                      <div class="form-section">
                          <h3>Server Editions & Connection Details</h3>
                          
                          <div class="form-group">
                              <label>Server Edition <span class="required">*</span></label>
                              <div class="server-editions-options">
                                  <label class="checkbox-label">
                                      <input type="checkbox" name="server_editions[]" value="java" id="edition_java" <?php checked(in_array('java', $server_editions)); ?>>
                                      <span class="checkmark"></span>
                                      <span>Java Edition</span>
                                  </label>
                                  
                                  <label class="checkbox-label">
                                      <input type="checkbox" name="server_editions[]" value="bedrock" id="edition_bedrock" <?php checked(in_array('bedrock', $server_editions)); ?>>
                                      <span class="checkmark"></span>
                                      <span>Bedrock Edition</span>
                                  </label>
                              </div>
                              <div class="field-note">Select all editions your server supports</div>
                          </div>
                          
                          <div id="java_fields" class="edition-fields" style="<?php echo in_array('java', $server_editions) ? 'display:block' : 'display:none'; ?>">
                              <div class="form-row">
                                  <div class="form-group">
                                      <label for="server_java_ip">Java Server IP <span class="required">*</span></label>
                                      <input type="text" name="server_java_ip" id="server_java_ip" value="<?php echo esc_attr($server_java_ip); ?>" <?php echo in_array('java', $server_editions) ? 'required' : ''; ?>>
                                  </div>
                                  
                                  <div class="form-group">
                                      <label for="server_port">Java Server Port</label>
                                      <input type="text" name="server_port" id="server_port" value="<?php echo esc_attr($server_port); ?>">
                                      <div class="field-note">Default is 25565 for Java</div>
                                  </div>
                              </div>
                          </div>
                          
                          <div id="bedrock_fields" class="edition-fields" style="<?php echo in_array('bedrock', $server_editions) ? 'display:block' : 'display:none'; ?>">
                              <div class="form-row">
                                  <div class="form-group">
                                      <label for="server_bedrock_ip">Bedrock Server IP <span class="required">*</span></label>
                                      <input type="text" name="server_bedrock_ip" id="server_bedrock_ip" value="<?php echo esc_attr($server_bedrock_ip); ?>" <?php echo in_array('bedrock', $server_editions) ? 'required' : ''; ?>>
                                  </div>
                                  
                                  <div class="form-group">
                                      <label for="server_bedrock_port">Bedrock Server Port</label>
                                      <input type="text" name="server_bedrock_port" id="server_bedrock_port" value="<?php echo esc_attr($server_bedrock_port); ?>">
                                      <div class="field-note">Default is 19132 for Bedrock</div>
                                  </div>
                              </div>
                          </div>
                          
                          <!-- For backward compatibility -->
                          <div class="form-group" style="display:none;">
                              <label for="server_ip">Server IP (Legacy)</label>
                              <input type="text" name="server_ip" id="server_ip" value="">
                          </div>
                      </div>
   				   <div class="form-section">
                          <h3>Server Details</h3>
                          
                          <div class="form-row">
                              <div class="form-group">
                                  <label for="server_version">Minecraft Version <span class="required">*</span></label>
                                  <select name="server_version" id="server_version" required>
                                      <option value="">Select Version</option>
                                      <?php foreach ($server_versions as $key => $label) : ?>
                                          <option value="<?php echo esc_attr($key); ?>" <?php selected($server_version, $key); ?>><?php echo esc_html($label); ?></option>
                                      <?php endforeach; ?>
                                  </select>
                              </div>
                              
                              <div class="form-group">
                                  <label for="server_country">Server Location <span class="required">*</span></label>
                                  <select name="server_country" id="server_country" required>
                                      <option value="">Select Country</option>
                                      <?php foreach ($server_countries as $key => $label) : ?>
                                          <option value="<?php echo esc_attr($key); ?>" <?php selected($server_country, $key); ?>><?php echo esc_html($label); ?></option>
                                      <?php endforeach; ?>
                                  </select>
                              </div>
                          </div>
                          
                          <div class="form-group">
                              <label>Server Types <span class="required">*</span></label>
                              <div class="server-types-grid">
                                  <?php foreach ($server_types as $key => $label) : ?>
                                      <label class="checkbox-label">
                                          <input type="checkbox" name="server_type[]" value="<?php echo esc_attr($key); ?>" <?php checked(in_array($key, $server_type)); ?>>
                                          <span class="checkmark"></span>
                                          <span><?php echo esc_html($label); ?></span>
                                      </label>
                                  <?php endforeach; ?>
                              </div>
                              <div class="field-note">Select up to 5 types that describe your server</div>
                          </div>
                      </div>
                      
                      <div class="form-section">
                          <h3>Media & Additional Information</h3>
                          
                          <div class="form-row">
                              <div class="form-group">
                                  <label for="server_logo">Server Logo (optional)</label>
                                  <input type="file" name="server_logo" id="server_logo" accept="image/png, image/jpeg, image/gif" class="file-input">
                                  <div class="field-note">Recommended size: 128x128 pixels (Max 1MB)</div>
                                  <div id="logo-preview-container" class="image-preview-container"></div>
                              </div>
                              
                              <div class="form-group">
                                  <label for="server_banner">Server Banner (optional)</label>
                                  <input type="file" name="server_banner" id="server_banner" accept="image/png, image/jpeg, image/gif" class="file-input">
                                  <div class="field-note">Recommended size: 1200x400 pixels (Max 2MB)</div>
                                  <div id="banner-preview-container" class="image-preview-container"></div>
                              </div>
                          </div>
                          
                          <div class="form-row">
                              <div class="form-group">
                                  <label for="server_website">Server Website</label>
                                  <input type="url" name="server_website" id="server_website" value="<?php echo esc_url($server_website); ?>">
                              </div>
                              
                              <div class="form-group">
                                  <label for="server_discord">Discord Invite URL</label>
                                  <input type="url" name="server_discord" id="server_discord" value="<?php echo esc_url($server_discord); ?>">
                              </div>
                          </div>
                      </div>
                      
                      <div class="form-actions">
                          <button type="submit" name="submit_minecraft_server" class="btn btn-submit">
                              <i class="fas fa-plus-circle"></i> Submit Server
                          </button>
                      </div>
                      
                      <div class="submission-terms">
                          By submitting your server, you agree to our <a href="/terms-and-conditions/" target="_blank" rel="noopener">Terms and Conditions</a>.
                      </div>
                  </form>
              </div>
          </div>
      </div>
  </div>
  <?php endif; ?>

  <!-- Server Categories Section -->
  <div class="server-categories-section">
      <div class="container">
          <div class="section-header">
              <h2><i class="fas fa-tags"></i> Browse By Category</h2>
              <p>Find your perfect Minecraft server by browsing our most popular categories.</p>
          </div>
          
          <div class="categories-grid">
              <a href="<?php echo esc_url(add_query_arg('type', 'survival', get_permalink())); ?>" class="category-card">
                  <div class="category-icon">
                      <i class="fas fa-tree"></i>
                  </div>
                  <h3>Survival</h3>
                  <p>Gather resources, build bases, and survive in the classic Minecraft experience.</p>
                  <span class="category-count">
                      <?php
                      $survival_count = count(get_posts(array(
                          'post_type' => 'minecraft_server',
                          'posts_per_page' => -1,
                          'meta_query' => array(
                              array(
                                  'key' => 'server_type',
                                  'value' => 'survival',
                                  'compare' => 'LIKE'
                              ),
                              array(
                                  'key' => 'server_approved',
                                  'value' => 'approved',
                                  'compare' => '='
                              )
                          ),
                          'fields' => 'ids'
                      )));
                      echo esc_html($survival_count) . ' servers';
                      ?>
                  </span>
              </a>
              
              <a href="<?php echo esc_url(add_query_arg('type', 'skyblock', get_permalink())); ?>" class="category-card">
                  <div class="category-icon">
                      <i class="fas fa-cloud"></i>
                  </div>
                  <h3>SkyBlock</h3>
                  <p>Start with limited resources on a floating island and expand your domain.</p>
                  <span class="category-count">
                      <?php
                      $skyblock_count = count(get_posts(array(
                          'post_type' => 'minecraft_server',
                          'posts_per_page' => -1,
                          'meta_query' => array(
                              array(
                                  'key' => 'server_type',
                                  'value' => 'skyblock',
                                  'compare' => 'LIKE'
                              ),
                              array(
                                  'key' => 'server_approved',
                                  'value' => 'approved',
                                  'compare' => '='
                              )
                          ),
                          'fields' => 'ids'
                      )));
                      echo esc_html($skyblock_count) . ' servers';
                      ?>
                  </span>
              </a>
              
              <a href="<?php echo esc_url(add_query_arg('type', 'factions', get_permalink())); ?>" class="category-card">
                  <div class="category-icon">
                      <i class="fas fa-flag"></i>
                  </div>
                  <h3>Factions</h3>
                  <p>Form alliances, build bases, and engage in strategic PvP as you claim territory.</p>
                  <span class="category-count">
                      <?php
                      $factions_count = count(get_posts(array(
                          'post_type' => 'minecraft_server',
                          'posts_per_page' => -1,
                          'meta_query' => array(
                              array(
                                  'key' => 'server_type',
                                  'value' => 'factions',
                                  'compare' => 'LIKE'
                              ),
                              array(
                                  'key' => 'server_approved',
                                  'value' => 'approved',
                                  'compare' => '='
                              )
                          ),
                          'fields' => 'ids'
                      )));
                      echo esc_html($factions_count) . ' servers';
                      ?>
                  </span>
              </a>
              
              <a href="<?php echo esc_url(add_query_arg('type', 'minigames', get_permalink())); ?>" class="category-card">
                  <div class="category-icon">
                      <i class="fas fa-gamepad"></i>
                  </div>
                  <h3>Minigames</h3>
                  <p>Enjoy a variety of quick, fun game modes from parkour to Bed Wars and more.</p>
                  <span class="category-count">
                      <?php
                      $minigames_count = count(get_posts(array(
                          'post_type' => 'minecraft_server',
                          'posts_per_page' => -1,
                          'meta_query' => array(
                              array(
                                  'key' => 'server_type',
                                  'value' => 'minigames',
                                  'compare' => 'LIKE'
                              ),
                              array(
                                  'key' => 'server_approved',
                                  'value' => 'approved',
                                  'compare' => '='
                              )
                          ),
                          'fields' => 'ids'
                      )));
                      echo esc_html($minigames_count) . ' servers';
                      ?>
                  </span>
              </a>
              
              <a href="<?php echo esc_url(add_query_arg('type', 'smp', get_permalink())); ?>" class="category-card">
                  <div class="category-icon">
                      <i class="fas fa-users"></i>
                  </div>
                  <h3>SMP</h3>
                  <p>Join a Survival Multiplayer community with friends and build together.</p>
                  <span class="category-count">
                      <?php
                      $smp_count = count(get_posts(array(
                          'post_type' => 'minecraft_server',
                          'posts_per_page' => -1,
                          'meta_query' => array(
                              array(
                                  'key' => 'server_type',
                                  'value' => 'smp',
                                  'compare' => 'LIKE'
                              ),
                              array(
                                  'key' => 'server_approved',
                                  'value' => 'approved',
                                  'compare' => '='
                              )
                          ),
                          'fields' => 'ids'
                      )));
                      echo esc_html($smp_count) . ' servers';
                      ?>
                  </span>
              </a>
              
              <a href="<?php echo esc_url(add_query_arg('type', 'creative', get_permalink())); ?>" class="category-card">
                  <div class="category-icon">
                      <i class="fas fa-paint-brush"></i>
                  </div>
                  <h3>Creative</h3>
                  <p>Build without limitations and unleash your creativity with unlimited resources.</p>
                  <span class="category-count">
                      <?php
                      $creative_count = count(get_posts(array(
                          'post_type' => 'minecraft_server',
                          'posts_per_page' => -1,
                          'meta_query' => array(
                              array(
                                  'key' => 'server_type',
                                  'value' => 'creative',
                                  'compare' => 'LIKE'
                              ),
                              array(
                                  'key' => 'server_approved',
                                  'value' => 'approved',
                                  'compare' => '='
                              )
                          ),
                          'fields' => 'ids'
                      )));
                      echo esc_html($creative_count) . ' servers';
                      ?>
                  </span>
              </a>
          </div>
          
          <div class="categories-cta">
              <a href="<?php echo esc_url(get_permalink()); ?>" class="btn btn-secondary">
                  <i class="fas fa-list"></i> View All Categories
              </a>
          </div>
      </div>
  </div>
</div>

<!-- Server List JavaScript -->
<script>
jQuery(document).ready(function($) {
    $(".toggle-add-server").on("click", function(e) {
       e.preventDefault(); 
       e.stopPropagation();

       const $addServerSection = $("#add-server");
       
       // Önce görünür yap, sonra animasyonu uygula
       $addServerSection.css('display', 'block');
       
       // Sayfayı bölüme doğru kaydır
       $("html, body").animate({
           scrollTop: $addServerSection.offset().top - 50
       }, 500);

       return false;
   });
   
   // Kapatma düğmesi ekleyin (isteğe bağlı)
   $('<button class="close-form-btn">&times;</button>').prependTo("#add-server .server-submission-form").on("click", function() {
       $("#add-server").slideUp(300);
   });

   // Toggle edition-specific IP fields
   function updateEditionFields() {
       const javaChecked = $("#edition_java").is(":checked");
       const bedrockChecked = $("#edition_bedrock").is(":checked");
       
       if (javaChecked) {
           $("#java_fields").slideDown();
           $("#server_java_ip").prop("required", true);
       } else {
           $("#java_fields").slideUp();
           $("#server_java_ip").prop("required", false);
       }
       
       if (bedrockChecked) {
           $("#bedrock_fields").slideDown();
           $("#server_bedrock_ip").prop("required", true);
       } else {
           $("#bedrock_fields").slideUp();
           $("#server_bedrock_ip").prop("required", false);
       }
       
       // For backwards compatibility
       if (javaChecked && $("#server_java_ip").val()) {
           $("#server_ip").val($("#server_java_ip").val());
       } else if (bedrockChecked && $("#server_bedrock_ip").val()) {
           $("#server_ip").val($("#server_bedrock_ip").val());
       }
   }
   
   // Initialize fields visibility
   updateEditionFields();
   
   // Add event listeners for edition checkboxes
   $("#edition_java, #edition_bedrock").on("change", updateEditionFields);
   
   // Server types limitation (max 5)
   $("input[name='server_type[]']").on("change", function() {
       if ($("input[name='server_type[]']:checked").length > 5) {
           this.checked = false;
           showNotification("You can select up to 5 server types", "warning");
       }
   });
   
   // File input preview for logo and banner
   $("#server_logo").on("change", function() {
       previewImage(this, 'logo-preview-container', 'Server Logo Preview');
   });
   
   $("#server_banner").on("change", function() {
       previewImage(this, 'banner-preview-container', 'Server Banner Preview');
   });
   
   function previewImage(input, containerId, altText) {
       if (input.files && input.files[0]) {
           const reader = new FileReader();
           reader.onload = function(e) {
               const $container = $('#' + containerId);
               $container.empty();
               $container.append(`<img src="${e.target.result}" alt="${altText}" class="image-preview">`);
           }
           reader.readAsDataURL(input.files[0]);
       }
   }
   
   // Form validation before submission
   $("#minecraft-server-form").on("submit", function(e) {
       let isValid = true;
       let errorMessage = "";
       
       // Check server title
       if ($("#server_title").val().trim() === "") {
           errorMessage += "Server name is required.<br>";
           isValid = false;
       }
       
       // Check server description
       if ($("#server_description").val().trim() === "") {
           errorMessage += "Server description is required.<br>";
           isValid = false;
       }
       
       // Check at least one edition is selected
       if (!$("#edition_java").is(":checked") && !$("#edition_bedrock").is(":checked")) {
           errorMessage += "Please select at least one server edition.<br>";
           isValid = false;
       }
       
       // Check Java IP if Java edition is selected
       if ($("#edition_java").is(":checked") && $("#server_java_ip").val().trim() === "") {
           errorMessage += "Java Server IP is required.<br>";
           isValid = false;
       }
       
       // Check Bedrock IP if Bedrock edition is selected
       if ($("#edition_bedrock").is(":checked") && $("#server_bedrock_ip").val().trim() === "") {
           errorMessage += "Bedrock Server IP is required.<br>";
           isValid = false;
       }
       
       // Check server version
       if ($("#server_version").val() === "") {
           errorMessage += "Please select a Minecraft version.<br>";
           isValid = false;
       }
       
       // Check server country
       if ($("#server_country").val() === "") {
           errorMessage += "Please select a server location.<br>";
           isValid = false;
       }
       
       // Check at least one server type is selected
       if ($("input[name='server_type[]']:checked").length === 0) {
           errorMessage += "Please select at least one server type.<br>";
           isValid = false;
       }
       
       if (!isValid) {
           e.preventDefault();
           $("html, body").animate({ scrollTop: $("#add-server").offset().top - 50 }, 500);
           showNotification(errorMessage, "error");
           return false;
       }
       
       // Show loading indicator
       $(".form-loader").addClass("active");
       return true;
   });
   
   // IP copy functionality
   $(".ip-copy, .server-ip-copy").on("click", function() {
       const ip = $(this).data("clipboard");
       const $button = $(this).find(".copy-ip-btn");
       const originalText = $button.html();
       
       if (navigator.clipboard && navigator.clipboard.writeText) {
           navigator.clipboard.writeText(ip)
               .then(() => {
                   showCopiedFeedback($button, originalText);
               })
               .catch(() => {
                   fallbackCopy(ip, $button, originalText);
               });
       } else {
           fallbackCopy(ip, $button, originalText);
       }
   });
   
   function fallbackCopy(text, $button, originalText) {
       const $textarea = $("<textarea>").css({
           position: "fixed",
           opacity: 0
       }).val(text);
       
       $("body").append($textarea);
       $textarea.select();
       
       try {
           const success = document.execCommand("copy");
           if (success) {
               showCopiedFeedback($button, originalText);
           }
       } catch (err) {
           console.error("Copy failed:", err);
       }
       
       $textarea.remove();
   }
   
   function showCopiedFeedback($button, originalText) {
       $button.html('<i class="fas fa-check"></i> Copied');
       setTimeout(() => {
           $button.html(originalText);
       }, 2000);
       
       showNotification("Server address copied to clipboard!", "success");
   }
   
   // Show notification messages
   function showNotification(message, type = "info") {
       // Remove any existing notifications
       $(".notification-toast").remove();
       
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
       
       $("body").append($notification);
       setTimeout(() => $notification.addClass("show"), 100);
       
       const hideNotification = () => {
           $notification.removeClass("show");
           setTimeout(() => $notification.remove(), 300);
       };
       
       setTimeout(hideNotification, 5000);
       $notification.find(".notification-close").on("click", hideNotification);
   }
   
   // Handle submission success message from URL parameter
   if (window.location.href.indexOf('submission_success=1') > -1) {
       showNotification("Your server has been submitted successfully and is pending approval.", "success");
   }
   
   // Country flag loading
   loadCountryFlags();
   
   function loadCountryFlags() {
       $(".country-flag").each(function() {
           const countryCode = $(this).data("country")?.toLowerCase();
           if (countryCode) {
               $(this).attr("src", `https://flagcdn.com/w20/${countryCode}.png`);
           }
       });
   }
   
   // Refresh server status
   function refreshServerStatus() {
       $('.server-card, .featured-server-card, .popular-server-card').each(function() {
           const $card = $(this);
           const serverId = $card.data('id');
           
           if (!serverId) return;
           
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
                       // Update the server status UI
                       updateServerStatusUI($card, response.data);
                   }
               }
           });
       });
   }
   
   function updateServerStatusUI($card, data) {
       // Update status indicator
       const $statusIndicator = $card.find('.server-status');
       $statusIndicator.removeClass('online offline')
           .addClass(data.overall_status)
           .find('.status-text')
           .text(data.overall_status === 'online' ? 'Online' : 'Offline');
       
       // Update player counts if the server is online
       if (data.overall_status === 'online') {
           const $playerCount = $card.find('.player-count-box');
           $playerCount.show();
           
           // Update Java player count if applicable
           if (data.java_status === 'online') {
               $card.find('.player-edition:eq(0) .current-players').text(data.java_player_count);
               $card.find('.player-edition:eq(0) .max-players').text(data.java_max_players);
           }
           
           // Update Bedrock player count if applicable
           if (data.bedrock_status === 'online') {
               $card.find('.player-edition:eq(1) .current-players').text(data.bedrock_player_count);
               $card.find('.player-edition:eq(1) .max-players').text(data.bedrock_max_players);
           }
           
           // Update overall player count
           const totalPlayers = data.java_player_count + data.bedrock_player_count;
           const totalMaxPlayers = data.java_max_players + data.bedrock_max_players;
           $card.find('.player-count .current-players').text(totalPlayers);
           $card.find('.player-count .max-players').text(totalMaxPlayers);
       } else {
           $card.find('.player-count-box').hide();
       }
       
       // Update last checked time
       $card.find('.status-last-checked').attr('title', 'Last checked: Just now');
   }
   
   // Periodically refresh server status
   setInterval(refreshServerStatus, 300000); // Every 5 minutes
   
   // Add refresh button click handler
   $(document).on('click', '.refresh-status-btn', function(e) {
       e.preventDefault();
       const $card = $(this).closest('.server-card, .featured-server-card, .popular-server-card');
       const serverId = $card.data('id');
       
       if (!serverId) return;
       
       // Show loading indicator
       $(this).addClass('refreshing').prop('disabled', true);
       
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
                   // Update the server status UI
                   updateServerStatusUI($card, response.data);
                   showNotification("Server status updated successfully!", "success");
               } else {
                   showNotification("Error updating server status", "error");
               }
           },
           error: function() {
               showNotification("Error checking server status", "error");
           },
           complete: function() {
               // Remove loading indicator
               $card.find('.refresh-status-btn').removeClass('refreshing').prop('disabled', false);
           }
       });
   });
});
</script>
<style>
/* Main Styles for Minecraft Server List */
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

.minecraft-server-list {
 font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
 color: var(--text-color);
 line-height: 1.6;
 background-color: var(--background-color);
}

.container {
 max-width: 1280px;
 margin: 0 auto;
 padding: 0 20px;
 position: relative; /* To ensure proper containing for absolute elements */
 overflow: visible; /* Make sure content can overflow if needed */
 box-sizing: border-box; /* Include padding in width calculations */
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

.notification.warning {
 background-color: #FFF8E1;
 border-left: 4px solid var(--warning-color);
 color: #FF8F00;
}

.notification.info {
 background-color: #E3F2FD;
 border-left: 4px solid var(--info-color);
 color: #0D47A1;
}

.error-list {
 margin: 10px 0 0;
 padding-left: 20px;
}

/* Modified Hero Section - Transparent Background */
.server-list-hero {
 background: none !important; /* Remove background image */
 padding: 40px 0;
 color: #333333; /* Dark text color for better visibility */
 text-align: center;
 margin-bottom: 40px;
 position: relative;
}

.hero-content {
 position: relative;
 z-index: 2;
 max-width: 800px;
 margin: 0 auto;
}

.server-list-hero h1 {
 font-size: 2.5em;
 margin-bottom: 20px;
 color: #333333; /* Dark text */
 text-shadow: none; /* Remove text shadow */
}

.hero-description {
 font-size: 1.2em;
 margin-bottom: 30px;
 color: #555555; /* Slightly lighter text for description */
 opacity: 1;
}

/* Stat Badges - Improved for transparent background */
.server-stats-badges {
 display: flex;
 flex-wrap: wrap;
 justify-content: center;
 gap: 20px;
 margin-bottom: 30px;
}

.stat-badge {
 background-color: white;
 box-shadow: 0 2px 10px rgba(0,0,0,0.1);
 padding: 15px 20px;
 border-radius: var(--border-radius);
 display: flex;
 align-items: center;
 gap: 15px;
 min-width: 180px;
}

.stat-icon {
 color: var(--primary-color);
 background-color: rgba(76, 175, 80, 0.1);
 width: 50px;
 height: 50px;
 display: flex;
 align-items: center;
 justify-content: center;
 border-radius: 50%;
 font-size: 24px;
}

.stat-content {
 text-align: left;
}

.stat-value {
 font-size: 22px;
 font-weight: 700;
 line-height: 1.2;
 color: #333333;
}

.stat-label {
 font-size: 14px;
 color: #666;
}

/* Button Colors for better visibility on transparent background */
.hero-actions {
 display: flex;
 justify-content: center;
 gap: 15px;
}

.btn {
 display: inline-flex;
 align-items: center;
 gap: 8px;
 padding: 12px 24px;
 border: none;
 border-radius: var(--border-radius);
 font-weight: 600;
 cursor: pointer;
 text-decoration: none;
 transition: var(--transition);
}

.btn i {
 font-size: 16px;
}

.btn-primary {
 background-color: var(--primary-color);
 color: white;
}

.btn-primary:hover {
 background-color: var(--primary-dark);
}

.btn-secondary {
 background-color: #f0f0f0;
 color: #333333;
}

.btn-secondary:hover {
 background-color: #e0e0e0;
}

/* Server Filters Section */
.server-filters-section {
 margin-bottom: 40px;
}

.section-header {
 text-align: center;
 margin-bottom: 30px;
}

.section-header h2 {
 font-size: 2em;
 margin-bottom: 10px;
 display: flex;
 align-items: center;
 justify-content: center;
 gap: 10px;
}

.section-header p {
 color: var(--text-light);
 max-width: 700px;
 margin: 0 auto;
}

.filters-card {
 background-color: var(--card-color);
 border-radius: var(--border-radius);
 box-shadow: var(--box-shadow);
 padding: 25px;
 margin-bottom: 20px;
}

.server-filters {
 display: flex;
 flex-direction: column;
 gap: 20px;
}

.filters-row {
 display: flex;
 flex-wrap: wrap;
 gap: 20px;
 align-items: flex-end;
}

.filter-group {
 flex: 1;
 min-width: 200px;
}

.filter-group label {
 display: flex;
 align-items: center;
 gap: 6px;
 margin-bottom: 8px;
 font-weight: 600;
 color: #555;
}

.form-control {
 width: 100%;
 padding: 12px 15px;
 border: 1px solid var(--border-color);
 border-radius: var(--border-radius);
 background-color: white;
 transition: var(--transition);
}

.form-control:focus {
 border-color: var(--primary-color);
 outline: none;
 box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1);
}

.search-group {
 flex: 2;
 min-width: 300px;
}

.checkbox-filters {
 display: flex;
 gap: 20px;
}

.checkbox-filter {
 display: flex;
 align-items: center;
 gap: 8px;
 cursor: pointer;
}

.checkbox-filter input[type="checkbox"] {
 width: 18px;
 height: 18px;
 cursor: pointer;
}

.status-online {
 color: var(--online-color);
}

.filter-actions {
 display: flex;
 gap: 10px;
}

.btn-apply {
 background-color: var(--primary-color);
 color: white;
}

.btn-apply:hover {
 background-color: var(--primary-dark);
}

.btn-reset {
 background-color: #f0f0f0;
 color: #555;
}

.btn-reset:hover {
 background-color: #e0e0e0;
}

/* Active Filters */
.active-filters {
 display: flex;
 flex-wrap: wrap;
 align-items: center;
 gap: 10px;
 margin-bottom: 20px;
 padding: 15px;
 background-color: #f9f9f9;
 border-radius: var(--border-radius);
}

.filters-label {
 font-weight: 600;
 color: #555;
 margin-right: 5px;
}

.filter-tag {
 display: inline-flex;
 align-items: center;
 gap: 8px;
 padding: 5px 12px;
 background-color: white;
 border: 1px solid var(--border-color);
 border-radius: 20px;
 color: var(--text-color);
 text-decoration: none;
 font-size: 14px;
 transition: var(--transition);
}

.filter-tag:hover {
 background-color: #f0f0f0;
}

.filter-tag i {
 color: #888;
}

.clear-all-filters {
 color: var(--error-color);
 font-size: 14px;
 margin-left: auto;
 text-decoration: none;
 font-weight: 500;
}

.clear-all-filters:hover {
 text-decoration: underline;
}

/* Featured Servers */
.featured-servers {
 margin-bottom: 40px;
}

.list-section-title {
 display: flex;
 align-items: center;
 gap: 10px;
 margin-bottom: 20px;
 font-size: 1.6em;
 border-bottom: 2px solid #f0f0f0;
 padding-bottom: 15px;
}

.list-section-title i {
 color: var(--accent-color);
}

.server-count {
 font-size: 16px;
 font-weight: normal;
 color: var(--text-light);
 margin-left: 10px;
}

.featured-servers-grid {
 display: grid;
 grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
 gap: 25px;
}

.featured-server-card {
 background-color: var(--card-color);
 border-radius: var(--border-radius);
 overflow: hidden;
 box-shadow: var(--box-shadow);
 transition: var(--transition);
}

.featured-server-card:hover {
 transform: translateY(-5px);
 box-shadow: 0 15px 25px rgba(0, 0, 0, 0.1);
}

.featured-banner-link {
 text-decoration: none;
 color: white;
 display: block;
}

.featured-banner {
 background-size: cover;
 background-position: center;
 height: 220px;
 position: relative;
 color: white;
 display: flex;
 flex-direction: column;
 justify-content: space-between;
 padding: 20px;
}

.featured-badge {
 position: absolute;
 top: 15px;
 right: 15px;
 background-color: var(--accent-color);
 color: white;
 font-size: 12px;
 font-weight: 600;
 padding: 5px 10px;
 border-radius: 20px;
 display: flex;
 align-items: center;
 gap: 5px;
 z-index: 5;
}

.server-edition {
 position: absolute;
 top: 15px;
 left: 15px;
 font-size: 12px;
 font-weight: 600;
 padding: 5px 12px;
 border-radius: 20px;
 z-index: 5;
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

.server-info-top {
 display: flex;
 align-items: center;
 gap: 15px;
 margin-bottom: 5px;
}

.server-logo {
 width: 70px;
 height: 70px;
 border-radius: var(--border-radius);
 overflow: hidden;
 border: 3px solid white;
 box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
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
 font-size: 24px;
 font-weight: bold;
 color: white;
 text-transform: uppercase;
}

.server-title {
 margin: 0;
 font-size: 1.4em;
 text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
 font-weight: 700;
 color: #ffffff;
}

.server-title a {
 color: white;
 text-decoration: none;
}

.server-details {
 display: flex;
 flex-direction: column;
 gap: 12px;
}

.server-meta-info {
 display: flex;
 flex-wrap: wrap;
 gap: 10px;
 align-items: center;
}

.server-status {
 display: flex;
 align-items: center;
 gap: 6px;
 font-size: 14px;
 font-weight: 600;
 background-color: rgba(0, 0, 0, 0.3);
 padding: 5px 10px;
 border-radius: 20px;
}

.server-status.online {
 color: #8eff8e;
}

.server-status.offline {
 color: #ff8e8e;
}

.server-status .status-indicator {
 width: 8px;
 height: 8px;
 border-radius: 50%;
 margin-right: 4px;
}

.server-status.online .status-indicator {
 background-color: var(--online-color);
}

.server-status.offline .status-indicator {
 background-color: var(--offline-color);
}

.server-status .status-label {
 font-weight: 700;
 margin-right: 5px;
}

.player-count-box {
 display: flex;
 flex-direction: column;
 gap: 5px;
 background-color: rgba(0, 0, 0, 0.3);
 padding: 5px 10px;
 border-radius: 10px;
}

.player-edition {
 display: flex;
 align-items: center;
 gap: 5px;
 font-size: 13px;
 color: rgba(255, 255, 255, 0.9);
}

.edition-icon {
 color: #ffffff;
 width: 18px;
 display: inline-block;
 text-align: center;
}

.player-count {
 font-size: 13px;
 color: rgba(255, 255, 255, 0.9);
}

.player-count .current-players,
.player-count .max-players,
.player-edition .current-players,
.player-edition .max-players {
 font-weight: 700;
 color: white;
}

.server-country, 
.server-version {
 display: flex;
 align-items: center;
 gap: 5px;
 font-size: 13px;
 padding: 4px 10px;
 background-color: rgba(0, 0, 0, 0.3);
 border-radius: 20px;
 color: white;
}

.country-flag {
 display: inline-block;
 width: 16px !important;
 height: 12px !important;
 border-radius: 2px;
 object-fit: cover;
}

/* Server rating display in cards */
.server-rating-display {
 display: flex;
 align-items: center;
 margin-top: 5px;
}

.rating-stars {
 color: #FFD700;
 font-size: 14px;
 display: flex;
 align-items: center;
 gap: 2px;
}

.rating-value {
 margin-left: 6px;
 font-weight: 600;
 color: white;
 font-size: 14px;
}

/* Server description preview */
.server-description-preview {
 font-size: 14px;
 line-height: 1.5;
 color: rgba(255, 255, 255, 0.9);
 background-color: rgba(0, 0, 0, 0.2);
 padding: 8px 12px;
 border-radius: 8px;
 margin-top: 5px;
 backdrop-filter: blur(2px);
}

.server-tags {
 display: flex;
 flex-wrap: wrap;
 gap: 8px;
}

.server-tag {
 background-color: rgba(255, 255, 255, 0.15);
 padding: 3px 10px;
 border-radius: 20px;
 font-size: 12px;
 color: white;
 text-decoration: none;
 transition: var(--transition);
}

.server-tag:hover {
 background-color: rgba(255, 255, 255, 0.25);
}

.server-tag.more {
 background-color: rgba(0, 0, 0, 0.2);
}

.server-tag.premium {
 background-color: rgba(255, 193, 7, 0.15);
 color: #F57F17;
}

.featured-server-footer {
 padding: 15px;
 display: flex;
 justify-content: space-between;
 align-items: center;
 flex-wrap: wrap;
 gap: 10px;
 border-top: 1px solid #f0f0f0;
}

.server-address {
 display: flex;
 flex-direction: column;
 gap: 10px;
}

.ip-container {
 display: flex;
 flex-direction: column;
}

.ip-label {
 font-size: 12px;
 color: var(--text-light);
 margin-bottom: 3px;
 font-weight: 600;
}

.ip-copy {
 display: flex;
 align-items: center;
 gap: 5px;
 background-color: #f9f9f9;
 padding: 5px 8px;
 border-radius: 4px;
 cursor: pointer;
}

.ip-text {
 font-family: 'Courier New', monospace;
 font-weight: 600;
 color: var(--text-color);
}

.copy-ip-btn {
 background: none;
 border: none;
 color: #888;
 cursor: pointer;
 padding: 3px;
 transition: var(--transition);
 min-width: 30px;
 width: 30px;
 flex-shrink: 0;
}

.copy-ip-btn:hover {
 color: var(--primary-color);
}

.bedrock-port {
 font-size: 12px;
 color: var(--text-light);
 margin-top: 2px;
 padding-left: 8px;
}

.port-label {
 font-weight: 600;
}

.server-stats {
 display: flex;
 gap: 15px;
}

.stat-votes, .stat-rating {
 display: flex;
 align-items: center;
 gap: 5px;
 font-size: 14px;
 color: var(--text-light);
}

.stat-votes i {
 color: var(--accent-color);
}

.stat-rating i {
 color: #FFC107;
}

.server-actions {
 display: flex;
 gap: 10px;
}

.btn-vote, .btn-view, .btn-join, .btn-discord {
 padding: 8px 15px;
 font-size: 14px;
}

.btn-vote {
 background-color: var(--accent-color);
 color: white;
}

.btn-vote:hover {
 background-color: #FB8C00;
}

.btn-view {
 background-color: #f0f0f0;
 color: #555;
}

.btn-view:hover {
 background-color: #e0e0e0;
}

.btn-join {
 background-color: var(--primary-color);
 color: white;
}

.btn-join:hover {
 background-color: var(--primary-dark);
}

.btn-discord {
 background-color: #7289DA;
 color: white;
}

.btn-discord:hover {
 background-color: #5B73C4;
}

/* Popular Servers Section (NEW) */
.popular-servers {
 margin-bottom: 40px;
}

.popular-servers-grid {
 display: grid;
 grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
 gap: 20px;
}

.popular-server-card {
 background-color: white;
 border-radius: var(--border-radius);
 overflow: hidden;
 box-shadow: var(--box-shadow);
 transition: var(--transition);
}

.popular-server-card:hover {
 transform: translateY(-5px);
 box-shadow: 0 15px 25px rgba(0,0,0,0.1);
}

.popular-server-link {
 text-decoration: none;
 color: white;
 display: block;
}

.popular-server-banner {
 background-size: cover;
 background-position: center;
 position: relative;
 height: 200px;
 padding: 15px;
 display: flex;
 flex-direction: column;
 justify-content: space-between;
}

.popular-badge {
 position: absolute;
 top: 15px;
 right: 15px;
 background-color: #E91E63;
 color: white;
 font-size: 12px;
 font-weight: 600;
 padding: 5px 10px;
 border-radius: 20px;
 z-index: 10;
 display: flex;
 align-items: center;
 gap: 5px;
}

.server-header-content {
 display: flex;
 gap: 15px;
 align-items: flex-start;
}

.server-title-area {
 flex: 1;
}

.server-footer-content {
 display: flex;
 justify-content: space-between;
 align-items: center;
}

.server-tags-container {
 display: flex;
 flex-wrap: wrap;
 gap: 8px;
}

.server-quick-actions {
 display: flex;
 gap: 30px;
}

.server-ip-copy {
 background-color: rgba(0,0,0,0.3);
 color: grey;
 padding: 12px 18px;
 border-radius: 8px;
 display: flex;
 align-items: center;
 gap: 8px;
 cursor: pointer;
}

.join-button {
 background-color: var(--primary-color);
 color: white;
 width: 32px;
 height: 32px;
 display: flex;
 align-items: center;
 justify-content: center;
 border-radius: 4px;
 transition: var(--transition);
}

.join-button:hover {
 background-color: var(--primary-dark);
}

/* Server Grid */
.servers-grid {
 display: grid;
 grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
 gap: 20px;
 margin-bottom: 40px;
 width: 100%;
}

/* UPDATED CARD STYLES WITH FIXED HEIGHT AND OVERFLOW CONTROL */
.server-card {
 background-color: var(--card-color);
 border-radius: var(--border-radius);
 overflow: hidden;
 box-shadow: var(--box-shadow);
 transition: var(--transition);
 display: flex;
 flex-direction: column;
 min-height: 480px; /* Increased fixed height */
 width: 100%;
 box-sizing: border-box;
}

.server-card:hover {
 transform: translateY(-3px);
 box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
}

.server-card-banner {
 position: relative;
 overflow: hidden;
}

/* Banner repetition prevention */
.server-banner-link {
 background-repeat: no-repeat !important;
 position: relative;
 color: white;
 padding: 15px;
 height: 220px;
 min-height: 220px;
 display: flex;
 flex-direction: column;
 justify-content: space-between;
 text-decoration: none;
 width: 100%;
 box-sizing: border-box;
 background-position: center top !important;
}

.server-badges {
 position: absolute;
 top: 10px;
 right: 10px;
 display: flex;
 gap: 5px;
 z-index: 5;
}

.server-badge {
 width: 24px;
 height: 24px;
 border-radius: 50%;
 display: flex;
 align-items: center;
 justify-content: center;
 color: white;
 font-size: 12px;
}

.server-badge.featured {
 background-color: #FFD700;
 color: #333;
}

.server-badge.sponsored {
 background-color: #8E24AA;
}

.server-rank-badge {
 position: absolute;
 top: 10px;
 left: 10px;
 width: 32px;
 height: 32px;
 display: flex;
 align-items: center;
 justify-content: center;
 background-color: rgba(0, 0, 0, 0.5);
 border-radius: 50%;
 z-index: 5;
}

.rank-number {
 font-weight: 700;
 color: white;
}

.server-header-info {
 display: flex;
 justify-content: space-between;
 margin-bottom: 15px;
}

.server-card .server-logo {
 width: 50px;
 height: 50px;
 border-radius: var(--border-radius);
 overflow: hidden;
 flex-shrink: 0;
 box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
 margin: 0;
 border: 2px solid white;
}

.server-card .server-edition {
 position: static;
 font-size: 11px;
 padding: 3px 8px;
 align-self: flex-start;
}

.server-card .server-title {
 font-size: 18px;
 margin-bottom: 10px;
}

.server-card .server-meta {
 display: flex;
 flex-wrap: wrap;
 gap: 10px;
 margin-bottom: 10px;
}

/* IMPROVED CONTENT STYLING */
.server-card-content {
 padding: 15px;
 flex: 1;
 display: flex;
 flex-direction: column;
 justify-content: space-between;
 width: 100%;
 box-sizing: border-box;
}

.server-card .server-description-preview {
 font-size: 14px;
 color: rgba(255, 255, 255, 0.9);
 line-height: 1.5;
 margin: 10px 0;
}

.server-card-footer {
 display: flex;
 flex-direction: column; /* Changed to vertical layout */
 gap: 10px;
 padding-top: 15px;
 border-top: 1px solid #f0f0f0;
 width: 100%;
}

/* IMPROVED IP SECTION */
.server-ips {
 display: flex;
 flex-direction: column;
 gap: 5px;
 width: 100%;
 box-sizing: border-box;
}

.server-ip-copy {
 display: flex;
 align-items: center;
 gap: 5px;
 background-color: #f9f9f9;
 padding: 5px 8px;
 border-radius: 4px;
 cursor: pointer;
 font-size: 13px;
 width: 100%;
 box-sizing: border-box;
 white-space: nowrap;
 overflow: hidden;
}

/* MODIFIED ACTIONS STYLING */
.server-card .server-actions {
 display: flex;
 align-items: center;
 justify-content: flex-end; /* Right-align buttons */
 gap: 8px;
 margin-top: 10px;
}

.server-card .btn {
 padding: 8px;
 min-width: 32px;
 height: 32px;
 display: inline-flex;
 align-items: center;
 justify-content: center;
}

/* No Servers Found */
.no-servers-found {
 text-align: center;
 padding: 60px 20px;
 background-color: white;
 border-radius: var(--border-radius);
 box-shadow: var(--box-shadow);
 margin-bottom: 40px;
}

.no-results-icon {
 font-size: 48px;
 color: #ddd;
 margin-bottom: 15px;
}

.no-servers-found h3 {
 margin: 0 0 10px;
}

.no-servers-found p {
 margin-bottom: 25px;
 max-width: 500px;
 margin-left: auto;
 margin-right: auto;
 color: var(--text-light);
}

.no-results-actions {
 display: flex;
 justify-content: center;
 gap: 15px;
}

/* Pagination */
.server-pagination {
 margin: 30px 0 40px;
 text-align: center;
}

.server-pagination .page-numbers {
 display: inline-block;
 padding: 8px 12px;
 margin: 0 3px;
 border: 1px solid #ddd;
 border-radius: var(--border-radius);
 background-color: white;
 color: var(--text-color);
 text-decoration: none;
 transition: var(--transition);
}

.server-pagination .page-numbers.current {
 background-color: var(--primary-color);
 color: white;
 border-color: var(--primary-color);
}

.server-pagination .page-numbers:hover {
 background-color: #f9f9f9;
}

.server-pagination .page-numbers.next,
.server-pagination .page-numbers.prev {
 padding: 8px 15px;
}

/* Add Server Section */
.add-server-section {
 margin: 60px 0;
 background-color: #f9f9f9;
 padding: 60px 0;
}

.add-server-content {
 display: flex;
 flex-wrap: wrap;
 gap: 40px;
 margin-top: 30px;
}

.add-server-benefits {
 flex: 1;
 min-width: 300px;
}

.add-server-benefits h3 {
 margin-top: 0;
 margin-bottom: 20px;
 font-size: 1.4em;
}

.benefits-list {
 list-style: none;
 padding: 0;
 margin: 0;
}

.benefits-list li {
 display: flex;
 align-items: flex-start;
 gap: 15px;
 margin-bottom: 20px;
 padding-bottom: 20px;
 border-bottom: 1px solid var(--border-color);
}

.benefits-list li:last-child {
 border-bottom: none;
}

.benefits-list li i {
 font-size: 20px;
 color: var(--primary-color);
 background-color: var(--primary-light);
 width: 40px;
 height: 40px;
 display: flex;
 align-items: center;
 justify-content: center;
 border-radius: 50%;
 flex-shrink: 0;
}

.benefit-content h4 {
 margin: 0 0 5px;
}

.benefit-content p {
 margin: 0;
 color: var(--text-light);
}

.server-submission-form {
 flex: 2;
 min-width: 500px;
 background-color: white;
 border-radius: var(--border-radius);
 box-shadow: var(--box-shadow);
 padding: 30px;
 position: relative;
}

/* Form Loader */
.form-loader {
 display: none;
 position: absolute;
 top: 0;
 left: 0;
 width: 100%;
 height: 100%;
 background-color: rgba(255, 255, 255, 0.8);
 display: none;
 position: absolute;
 top: 0;
 left: 0;
 width: 100%;
 height: 100%;
 background-color: rgba(255, 255, 255, 0.8);
 border-radius: var(--border-radius);
 z-index: 100;
 justify-content: center;
 align-items: center;
}

.form-loader.active {
 display: flex;
}

.loader {
 border: 5px solid #f3f3f3;
 border-radius: 50%;
 border-top: 5px solid var(--primary-color);
 width: 50px;
 height: 50px;
 animation: spin 2s linear infinite;
}

@keyframes spin {
 0% { transform: rotate(0deg); }
 100% { transform: rotate(360deg); }
}

/* Form Sections */
.form-section {
 margin-bottom: 30px;
 background-color: #fff;
 border: 1px solid var(--border-color);
 border-radius: var(--border-radius);
 padding: 20px;
}

.form-section h3 {
 margin-top: 0;
 margin-bottom: 20px;
 padding-bottom: 10px;
 border-bottom: 1px solid #f0f0f0;
 font-size: 16px;
}

.form-group {
 margin-bottom: 20px;
}

.form-row {
 display: flex;
 flex-wrap: wrap;
 gap: 20px;
 margin-bottom: 0;
}

.form-row .form-group {
 flex: 1;
 min-width: 200px;
}

.form-group label {
 display: block;
 margin-bottom: 8px;
 font-weight: 600;
}

.required {
 color: var(--error-color);
}

.form-group input[type="text"],
.form-group input[type="url"],
.form-group select,
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
.form-group select:focus,
.form-group textarea:focus {
 border-color: var(--primary-color);
 outline: none;
 box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1);
}

.field-note {
 margin-top: 5px;
 font-style: italic;
 font-size: 12px;
 color: #777;
}

.file-input {
 padding: 10px 0;
}

/* Image Preview */
.image-preview-container {
 margin-top: 10px;
}

.image-preview {
 max-width: 100%;
 max-height: 200px;
 border-radius: var(--border-radius);
 border: 1px solid var(--border-color);
}

/* Server Types Grid */
.server-types-grid {
 display: grid;
 grid-template-columns: repeat(3, 1fr);
 gap: 10px;
 margin-bottom: 10px;
}

/* Checkbox Styling */
.checkbox-label {
 display: flex;
 align-items: center;
 gap: 8px;
 padding: 8px 12px;
 background-color: #f9f9f9;
 border-radius: var(--border-radius);
 font-size: 14px;
 cursor: pointer;
 transition: var(--transition);
 position: relative;
 padding-left: 35px;
}

.checkbox-label:hover {
 background-color: #f0f0f0;
}

.checkbox-label input {
 position: absolute;
 opacity: 0;
 height: 0;
 width: 0;
}

.checkmark {
 position: absolute;
 top: 50%;
 left: 10px;
 transform: translateY(-50%);
 height: 18px;
 width: 18px;
 background-color: #eee;
 border-radius: 3px;
}

.checkbox-label:hover input ~ .checkmark {
 background-color: #ccc;
}

.checkbox-label input:checked ~ .checkmark {
 background-color: var(--primary-color);
}

.checkmark:after {
 content: "";
 position: absolute;
 display: none;
}

.checkbox-label input:checked ~ .checkmark:after {
 display: block;
}

.checkbox-label .checkmark:after {
 left: 6px;
 top: 3px;
 width: 5px;
 height: 10px;
 border: solid white;
 border-width: 0 2px 2px 0;
 transform: rotate(45deg);
}

.server-editions-options {
 display: flex;
 flex-wrap: wrap;
 gap: 20px;
 margin-bottom: 10px;
}

.edition-fields {
 background-color: #f9f9f9;
 border-radius: var(--border-radius);
 margin-bottom: 20px;
 padding: 15px;
 border: 1px solid #eee;
}

.form-actions {
 margin-top: 30px;
 text-align: center;
}

.btn-submit {
 background-color: var(--primary-color);
 color: white;
 font-size: 16px;
 padding: 14px 30px;
}

.btn-submit:hover {
 background-color: var(--primary-dark);
}

.submission-terms {
 margin-top: 20px;
 text-align: center;
 font-size: 14px;
 color: var(--text-light);
}

.submission-terms a {
 color: var(--primary-color);
 text-decoration: none;
}

.submission-terms a:hover {
 text-decoration: underline;
}

/* Server Categories Section */
.server-categories-section {
 margin: 60px 0;
}

.categories-grid {
 display: grid;
 grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
 gap: 20px;
 margin-bottom: 30px;
}

.category-card {
 background-color: white;
 border-radius: var(--border-radius);
 box-shadow: var(--box-shadow);
 padding: 25px;
 text-decoration: none;
 color: var(--text-color);
 transition: var(--transition);
 display: flex;
 flex-direction: column;
 height: 100%;
}

.category-card:hover {
 transform: translateY(-5px);
 box-shadow: 0 15px 25px rgba(0, 0, 0, 0.1);
}

.category-icon {
 width: 60px;
 height: 60px;
 background-color: var(--primary-light);
 color: var(--primary-color);
 border-radius: 50%;
 display: flex;
 align-items: center;
 justify-content: center;
 font-size: 24px;
 margin-bottom: 15px;
}

.category-card:nth-child(2) .category-icon {
 background-color: rgba(33, 150, 243, 0.1);
 color: var(--secondary-color);
}

.category-card:nth-child(3) .category-icon {
 background-color: rgba(244, 67, 54, 0.1);
 color: var(--error-color);
}

.category-card:nth-child(4) .category-icon {
 background-color: rgba(156, 39, 176, 0.1);
 color: #9C27B0;
}

.category-card:nth-child(5) .category-icon {
 background-color: rgba(255, 193, 7, 0.1);
 color: var(--warning-color);
}

.category-card:nth-child(6) .category-icon {
 background-color: rgba(0, 150, 136, 0.1);
 color: #009688;
}

.category-card h3 {
 margin: 0 0 10px;
 font-size: 1.3em;
}

.category-card p {
 color: var(--text-light);
 margin: 0 0 15px;
 flex: 1;
}

.category-count {
 font-size: 14px;
 color: var(--primary-color);
 font-weight: 600;
}

.categories-cta {
 text-align: center;
}

/* SEO Content Section */
.server-seo-content {
 margin: 60px 0;
 padding: 60px 0;
 background-color: #f9f9f9;
}

.seo-section {
 max-width: 900px;
 margin: 0 auto;
}

.seo-section h2 {
 margin-top: 0;
 margin-bottom: 30px;
 font-size: 2em;
 text-align: center;
}

.seo-section h3 {
 margin-top: 40px;
 margin-bottom: 20px;
 font-size: 1.6em;
 color: var(--primary-dark);
}

.seo-section h4 {
 margin-top: 30px;
 margin-bottom: 15px;
 font-size: 1.3em;
}

.seo-section p {
 margin-bottom: 20px;
 line-height: 1.7;
}

.seo-section ul {
 margin-bottom: 30px;
}

.seo-section li {
 margin-bottom: 10px;
}

.faq-section {
 margin: 30px 0;
}

.faq-section details {
 background-color: white;
 border-radius: var(--border-radius);
 margin-bottom: 15px;
 overflow: hidden;
 box-shadow: var(--box-shadow);
}

.faq-section summary {
 padding: 15px 20px;
 cursor: pointer;
 font-weight: 600;
 position: relative;
 outline: none;
}

.faq-section summary::-webkit-details-marker {
 display: none;
}

.faq-section summary::after {
 content: '+';
 position: absolute;
 right: 20px;
 top: 50%;
 transform: translateY(-50%);
 font-size: 20px;
 color: var(--primary-color);
}

.faq-section details[open] summary::after {
 content: '-';
}

.faq-section details p {
 padding: 0 20px 20px;
 margin: 0;
}

.server-type-descriptions {
 display: grid;
 grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
 gap: 20px;
 margin: 30px 0;
}

.type-description {
 background-color: white;
 border-radius: var(--border-radius);
 padding: 20px;
 box-shadow: var(--box-shadow);
}

.type-description h4 {
 margin-top: 0;
 margin-bottom: 10px;
 color: var(--primary-color);
}

.type-description p {
 margin: 0;
 color: var(--text-light);
}

.conclusion {
 margin-top: 40px;
 padding: 20px;
 background-color: var(--primary-light);
 border-radius: var(--border-radius);
 font-weight: 600;
 color: var(--primary-dark);
 text-align: center;
}

/* Responsive Adjustments */
@media (max-width: 1200px) {
 .server-list-hero {
   padding: 40px 0;
 }
 
 .server-list-hero h1 {
   font-size: 2.2em;
 }
 
 .stat-badge {
   min-width: 160px;
 }
}

@media (max-width: 992px) {
 .servers-grid {
   grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
 }
 
 .featured-servers-grid {
   grid-template-columns: 1fr;
 }
 
 .popular-servers-grid {
   grid-template-columns: 1fr;
 }
}

@media (max-width: 768px) {
 .server-list-hero h1 {
   font-size: 1.8em;
 }
 
 .hero-description {
   font-size: 1.1em;
 }
 
 .server-stats-badges {
   display: grid;
   grid-template-columns: repeat(2, 1fr);
   grid-gap: 15px;
 }
 
 .filters-row {
   flex-direction: column;
   gap: 15px;
 }
 
 .filter-group, .search-group {
   width: 100%;
 }
 
 .checkbox-filters {
   flex-direction: column;
   gap: 10px;
   align-items: flex-start;
 }
 
 .filter-actions {
   flex-direction: column;
   width: 100%;
 }
 
 .filter-actions .btn {
   width: 100%;
 }
 
 .server-card-footer {
   flex-wrap: wrap;
 }
 
 .server-ips {
   width: 100%;
   margin-bottom: 10px;
 }
 
 .server-actions {
   flex: 1;
   justify-content: flex-end;
 }
 
 .add-server-content {
   flex-direction: column;
 }
 
 .server-submission-form {
   min-width: unset;
 }
 
 .categories-grid {
   grid-template-columns: 1fr;
 }
 
 .server-types-grid {
   grid-template-columns: repeat(2, 1fr);
 }
}

@media (max-width: 576px) {
 .server-list-hero {
   padding: 30px 0;
 }
 
 .server-list-hero h1 {
   font-size: 1.6em;
 }
 
 .server-stats-badges {
   grid-template-columns: 1fr;
 }
 
 .hero-actions {
   flex-direction: column;
   width: 100%;
 }
 
 .hero-actions .btn {
   width: 100%;
 }
 
 .servers-grid {
   grid-template-columns: 1fr;
 }
 
 .categories-cta .btn {
   width: 100%;
 }
 
 .server-types-grid {
   grid-template-columns: 1fr;
 }
 
 .server-editions-options {
   flex-direction: column;
   gap: 10px;
 }
}

/* Additional fixes for the overflow issue */
.ip-text {
 text-overflow: ellipsis;
 overflow: hidden;
 max-width: calc(100% - 40px); /* Leave room for the copy button */
}

/* Fix for making sure the server cards have enough height */
.server-card {
 min-height: 480px; /* Increased minimum height */
}

.server-card .server-banner-link {
 height: 220px;
 background-size: cover;
 background-position: center;
}

/* Fix for IP container width in card footer */
.server-card-footer {
 display: grid;
 grid-template-columns: 1fr;
 gap: 12px;
 width: 100%;
}

.server-ip-copy {
 width: 100%;
 overflow: hidden;
 text-overflow: ellipsis;
 white-space: nowrap;
}

/* Make sure server actions stay in one row */
.server-actions {
 display: flex;
 justify-content: flex-end;
 flex-wrap: nowrap;
 gap: 8px;
}

/* Fix for images and banner heights */
.server-banner-link img,
.featured-banner img {
 width: 100%;
 height: 100%;
 object-fit: cover;
}

/* Ensure consistent width for server cards */
.servers-grid > * {
 box-sizing: border-box;
 width: 100%;
}

/* Tooltip stylesheet for showing full IP on hover */
.tooltip {
 position: relative;
 display: inline-block;
}

.tooltip .tooltiptext {
 visibility: hidden;
 width: auto;
 min-width: 120px;
 background-color: #555;
 color: #fff;
 text-align: center;
 border-radius: 6px;
 padding: 5px 10px;
 position: absolute;
 z-index: 1;
 bottom: 125%;
 left: 50%;
 transform: translateX(-50%);
 opacity: 0;
 transition: opacity 0.3s;
 white-space: nowrap;
}

.tooltip:hover .tooltiptext {
 visibility: visible;
 opacity: 1;
}

/* Fix for the banner overflow issue */
.server-banner-link {
 position: relative;
 height: 220px !important;
 min-height: 220px !important;
 max-height: 220px !important;
 overflow: hidden;
 display: flex;
 flex-direction: column;
 justify-content: space-between;
 background-position: center top !important;
 background-size: cover !important;
 background-repeat: no-repeat !important;
}

/* Ensure server tags stay within the banner */
.server-tags {
 position: relative;
 bottom: 0;
 left: 0;
 width: 100%;
 display: flex;
 flex-wrap: wrap;
 gap: 8px;
 margin-top: auto;
 z-index: 5;
}

/* Fix server card to prevent content overflow */
.server-card {
 display: flex;
 flex-direction: column;
 min-height: 480px;
 height: auto;
 overflow: hidden;
}

.server-card-banner {
 position: relative;
 height: 220px;
 min-height: 220px;
 max-height: 220px;
 overflow: hidden;
}

/* Add a background to the card content to separate from banner */
.server-card-content {
 background-color: #FFFFFF;
 flex: 1;
 padding: 15px;
 border-top: none;
 margin-top: 0;
}

/* Improve layout for server meta info */
.server-meta {
 margin-bottom: 15px !important;
}

/* Make server description more consistent */
.server-description-preview {
 height: 60px;
 overflow: hidden;
 text-overflow: ellipsis;
 display: -webkit-box;
 -webkit-line-clamp: 3;
 -webkit-box-orient: vertical;
 margin-bottom: 15px !important;
}

/* Fix footer alignment */
.server-card-footer {
 padding-top: 15px;
 border-top: 1px solid #f0f0f0;
 margin-top: auto;
}

/* Server card content tags style */
.server-card-content .server-tags {
 display: flex;
 flex-wrap: wrap;
 gap: 8px;
 margin-bottom: 15px; /* Footer spacing */
 padding: 0 5px; /* Light inner padding */
 justify-content: flex-start; /* Left-align tags */
}

.server-card-content .server-tag {
 background-color: #f0f0f0; /* Light gray background */
 color: var(--text-color);
 padding: 5px 12px;
 border-radius: 20px;
 font-size: 13px;
 text-decoration: none;
 transition: var(--transition);
}

.server-card-content .server-tag:hover {
 background-color: #e0e0e0;
}

.server-card-content .server-tag.more {
 background-color: #e9ecef;
 color: #6c757d;
}

.server-card-content .server-tag.premium {
 background-color: rgba(255, 193, 7, 0.15);
 color: #F57F17;
}

/* Hide tags in banner */
.server-banner-link .server-tags {
 display: none; /* Hide tags from banner */
}
/* New flex container for hero section */
.hero-flex-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items: center;
    margin-top: 30px;
    gap: 20px;
}

/* Modified stat badges for horizontal layout */
.server-stats-badges {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    flex: 1;
    justify-content: flex-start;
}

.stat-badge {
    background-color: white;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    padding: 12px 15px;
    border-radius: var(--border-radius);
    display: flex;
    align-items: center;
    gap: 10px;
    min-width: 150px;
    flex: 0 0 auto;
}

.stat-icon {
    width: 40px;
    height: 40px;
    font-size: 20px;
}

.stat-value {
    font-size: 20px;
}

/* Hero actions styling */
.hero-actions {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}

/* Responsive adjustments */
@media (max-width: 992px) {
    .hero-flex-container {
        flex-direction: column;
        align-items: center;
    }
    
    .server-stats-badges {
        justify-content: center;
        margin-bottom: 20px;
    }
}

@media (max-width: 768px) {
    .server-stats-badges {
        gap: 15px;
    }
    
    .stat-badge {
        min-width: 140px;
        padding: 10px;
    }
}

@media (max-width: 576px) {
    .hero-actions {
        width: 100%;
        flex-direction: column;
    }
    
    .hero-actions .btn {
        width: 100%;
    }
}
</style>
<?php get_footer(); ?>