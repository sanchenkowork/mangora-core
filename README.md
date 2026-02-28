# Mangora Core

A lightweight, scalable WordPress plugin for manga streaming with episode management.

## Description

Mangora Core provides a complete manga streaming solution for WordPress. It registers custom post types for manga and episodes, includes taxonomies for genres and status, and provides a modern frontend interface with AJAX filtering and video playback.

## Features

- **Custom Post Types**: Manga and Episode management
- **Taxonomies**: Genre (hierarchical) and Status (controlled vocabulary)
- **Meta Fields**: Year, Rating, Video URLs, Episode Numbers
- **AJAX Filtering**: Filter manga by genre, status, and year without page reload
- **Video Player**: Built-in player supporting YouTube, Vimeo, and direct video files
- **Responsive Design**: Mobile-friendly grid layouts
- **Security**: Nonce verification, input sanitization, and output escaping throughout
- **Clean Architecture**: Object-oriented classes with WordPress coding standards

## Installation

1. Upload the plugin files to `/wp-content/plugins/mangora-core/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Create manga entries via the 'Manga' menu
4. Add episodes via 'Manga > Episodes' and link them to manga

## Usage

### Adding Manga

1. Navigate to **Manga > Add New**
2. Enter title, description, and featured image
3. Set metadata: Year, Rating (0-10), Status
4. Assign Genres
5. Publish

### Adding Episodes

1. Navigate to **Manga > Episodes > Add New**
2. Select the parent Manga from dropdown
3. Enter episode number (supports decimals for OVAs)
4. Add video URL (YouTube, Vimeo, or direct link)
5. Set duration (optional)
6. Publish

### Frontend Display

- Archive page at `/manga/` with filters
- Single manga page with episode list
- Click any episode to open the video player modal

## Architecture

```
mangora-core/
├── mangora-core.php          # Main plugin file
├── includes/
│   ├── class-post-types.php  # CPT & taxonomy registration
│   ├── class-meta-boxes.php  # Admin meta fields
│   ├── class-ajax.php        # AJAX filtering handlers
│   ├── class-template.php    # Frontend template loader
│   └── class-template-helper.php # Template helper functions
├── assets/
│   ├── css/
│   │   ├── admin.css
│   │   └── frontend.css
│   └── js/
│       └── frontend.js
└── templates/
    ├── archive-manga.php     # Manga listing with filters
    └── single-manga.php      # Single manga with episodes
```

## Security

- All forms use WordPress nonce verification
- Input data sanitized using `absint()`, `sanitize_text_field()`, `esc_url_raw()`
- Output escaped using `esc_attr()`, `esc_html()`, `esc_url()`
- Capability checks on all admin actions
- Prepared queries via WP_Query/WP_Meta_Query

## Screenshots

*Screenshots section - add images to your README:*

1. **Admin - Manga Edit Screen**: Shows custom meta boxes for year, rating, status
2. **Admin - Episode Edit Screen**: Shows manga selection dropdown and video URL field
3. **Frontend - Manga Archive**: Grid layout with filter sidebar
4. **Frontend - Single Manga**: Poster, metadata, and episode list
5. **Frontend - Video Player**: Modal with embedded video player

## Customization

### Template Override

To override templates in your theme, copy files from `templates/` to your theme's root and modify.

### CSS Customization

Target these CSS classes for styling:
- `.mangora-archive` - Archive page container
- `.mangora-single` - Single manga page
- `.manga-card` - Individual manga card
- `.episode-item` - Episode list item
- `.mangora-modal` - Video player modal

## Requirements

- WordPress 5.8+
- PHP 7.4+
- jQuery (bundled with WordPress)

## License

GPL v2 or later

## Credits

Built with WordPress coding standards and modern best practices.
