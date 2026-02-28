# Mangora Core - WordPress Manga Streaming Plugin

## Step 1: Architecture Design

### Custom Post Types

1. **manga** - Main content type for manga series
   - Supports: title, editor, thumbnail, excerpt
   - Archive enabled for browsing
   - Hierarchical: false (flat structure)

2. **episode** - Individual episodes linked to manga
   - Supports: title, editor
   - Hierarchical: true (children of manga via meta relationship)
   - No archive (displayed within manga context)

### Taxonomies

1. **genre** (manga) - Hierarchical, similar to categories
   - Examples: Action, Romance, Fantasy, Horror
   - Multiple genres per manga

2. **status** (manga) - Non-hierarchical, like tags but controlled
   - Examples: Ongoing, Completed, Hiatus, Cancelled
   - Single status per manga (enforced via meta)

### Meta Fields

#### Manga Meta (stored in postmeta):
| Field | Type | Description |
|-------|------|-------------|
| _manga_year | int | Release year |
| _manga_rating | float | Rating (0-10) |
| _manga_status | string | Ongoing/Completed/Hiatus |

#### Episode Meta (stored in postmeta):
| Field | Type | Description |
|-------|------|-------------|
| _episode_manga_id | int | Parent manga post ID |
| _episode_number | float | Episode/chapter number (float for 1.5, OVA) |
| _episode_video_url | string | Video embed URL (iframe compatible) |
| _episode_duration | string | Duration string (e.g., "24:00") |

### Relationships

- **Episode → Manga**: One-to-many via `_episode_manga_id` meta field
- Query pattern: Get all episodes where `_episode_manga_id` = manga_id, order by `_episode_number`

### URL Structure

- `/manga/` - Archive page with filters
- `/manga/slime-reincarnation/` - Single manga with episodes list
- (Episodes displayed via AJAX/modal on single manga page)

### Database Query Strategy

1. **Manga Listing**: Standard WP_Query with taxonomy joins for filtering
2. **Episodes**: Meta query on `_episode_manga_id` with numeric sorting
3. **Filtering**: AJAX endpoint using transient caching for filter combinations

### File Structure

```
mangora-core/
├── mangora-core.php          # Main plugin file
├── includes/
│   ├── class-post-types.php  # CPT & taxonomy registration
│   ├── class-meta-boxes.php  # Admin meta fields
│   ├── class-ajax.php        # AJAX filtering handlers
│   └── class-template.php    # Frontend template overrides
├── assets/
│   ├── css/
│   │   ├── admin.css
│   │   └── frontend.css
│   └── js/
│       ├── admin.js
│       └── frontend.js
└── templates/
    ├── archive-manga.php     # Manga listing with filters
    └── single-manga.php      # Single manga with episodes
```

---

## Step 2: Minimal MVP Plan

### Phase 1: Core Foundation
- [x] Plugin header and activation hooks
- [x] Register CPT: manga, episode
- [x] Register Taxonomies: genre, status
- [x] Basic admin meta boxes

### Phase 2: Admin Experience
- [x] Manga meta: year, rating, status
- [x] Episode meta: manga selection, episode number, video URL
- [x] Admin CSS/JS for UX

### Phase 3: Frontend
- [x] Template loader for archive-manga
- [x] Template loader for single-manga
- [x] Episode list on single manga page
- [x] Video player modal/lightbox

### Phase 4: Filtering
- [x] AJAX filter endpoint
- [x] Genre filter UI
- [x] Status filter UI
- [x] Year filter UI

### Phase 5: Polish
- [x] Security audit (nonces, sanitization)
- [x] Responsive CSS
- [x] README documentation
