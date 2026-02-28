(function($) {
    'use strict';

    // Filter form submission
    $('#mangora-filter-form').on('submit', function(e) {
        e.preventDefault();
        loadManga(1);
    });

    // Reset filter
    $('#mangora-filter-form').on('reset', function(e) {
        setTimeout(function() {
            loadManga(1);
        }, 10);
    });

    // Load manga with filters
    function loadManga(page) {
        var form = $('#mangora-filter-form');
        var genres = form.find('[name="genre[]"]').val() || [];
        var status = form.find('[name="status"]').val();
        var year = form.find('[name="year"]').val();

        $.ajax({
            url: mangoraData.ajaxUrl,
            type: 'POST',
            data: {
                action: 'mangora_filter_manga',
                nonce: mangoraData.filterNonce,
                genre: genres,
                status: status,
                year: year,
                paged: page
            },
            beforeSend: function() {
                $('#mangora-results').html('<span class="loading">Loading...</span>');
            },
            success: function(response) {
                if (response.success) {
                    renderResults(response.data.results);
                    renderPagination(response.data.total_pages, response.data.current_page);
                }
            }
        });
    }

    // Render manga cards
    function renderResults(results) {
        var html = '';
        if (results.length === 0) {
            html = '<p class="no-results">No manga found.</p>';
        } else {
            $.each(results, function(i, manga) {
                var poster = manga.thumbnail ? 
                    '<img src="' + escapeHtml(manga.thumbnail) + '" alt="">' :
                    '<div class="no-poster">No Image</div>';
                var status = manga.status ? 
                    '<span class="card-status status-' + escapeHtml(manga.status) + '">' + 
                    escapeHtml(manga.status.charAt(0).toUpperCase() + manga.status.slice(1)) + 
                    '</span>' : '';
                var rating = manga.rating ? '<span class="card-rating">' + manga.rating + ' ★</span>' : '';

                html += '<article class="manga-card">' +
                    '<a href="' + escapeHtml(manga.permalink) + '" class="card-link">' +
                        '<div class="card-poster">' + poster + status + '</div>' +
                        '<div class="card-info">' +
                            '<h3 class="card-title">' + escapeHtml(manga.title) + '</h3>' +
                            rating +
                        '</div>' +
                    '</a>' +
                '</article>';
            });
        }
        $('#mangora-results').html(html);
    }

    // Render pagination
    function renderPagination(totalPages, currentPage) {
        if (totalPages <= 1) {
            $('#mangora-pagination').hide();
            return;
        }

        var html = '';
        for (var i = 1; i <= totalPages; i++) {
            var className = i === currentPage ? 'page-numbers current' : 'page-numbers';
            html += '<a href="#" class="' + className + '" data-page="' + i + '">' + i + '</a>';
        }

        $('#mangora-pagination').html(html).show();
    }

    // Pagination click
    $(document).on('click', '#mangora-pagination .page-numbers', function(e) {
        e.preventDefault();
        var page = $(this).data('page');
        loadManga(page);
    });

    // Load episodes on single manga page
    var episodesContainer = $('#mangora-episodes');
    if (episodesContainer.length) {
        var mangaId = episodesContainer.data('manga-id');
        loadEpisodes(mangaId);
    }

    function loadEpisodes(mangaId) {
        $.ajax({
            url: mangoraData.ajaxUrl,
            type: 'POST',
            data: {
                action: 'mangora_get_episodes',
                nonce: mangoraData.episodesNonce,
                manga_id: mangaId
            },
            success: function(response) {
                if (response.success) {
                    renderEpisodes(response.data);
                }
            }
        });
    }

    function renderEpisodes(episodes) {
        var html = '';
        if (episodes.length === 0) {
            html = '<p>No episodes available.</p>';
        } else {
            $.each(episodes, function(i, ep) {
                var duration = ep.duration ? '<span class="episode-duration">' + escapeHtml(ep.duration) + '</span>' : '';
                html += '<div class="episode-item" data-episode-id="' + ep.id + '">' +
                    '<span class="episode-number">' + parseFloat(ep.number).toFixed(1) + '</span>' +
                    '<div class="episode-info">' +
                        '<h4 class="episode-title">' + escapeHtml(ep.title) + '</h4>' +
                        duration +
                    '</div>' +
                    '<span class="episode-play">▶</span>' +
                '</div>';
            });
        }
        episodesContainer.html(html);
    }

    // Episode click - open player
    $(document).on('click', '.episode-item', function() {
        var episodeId = $(this).data('episode-id');
        loadEpisodePlayer(episodeId);
    });

    function loadEpisodePlayer(episodeId) {
        $.ajax({
            url: mangoraData.ajaxUrl,
            type: 'POST',
            data: {
                action: 'mangora_get_episode_player',
                nonce: mangoraData.episodesNonce,
                episode_id: episodeId
            },
            success: function(response) {
                if (response.success) {
                    openPlayer(response.data);
                }
            }
        });
    }

    function openPlayer(data) {
        var modal = $('#mangora-player-modal');
        var container = $('#mangora-player-container');
        
        // Check if URL is an iframe embed or direct video
        var content;
        if (data.video_url.includes('youtube.com/embed') || data.video_url.includes('vimeo.com')) {
            content = '<iframe src="' + escapeHtml(data.video_url) + '" allowfullscreen></iframe>';
        } else {
            content = '<video controls autoplay><source src="' + escapeHtml(data.video_url) + '"></video>';
        }
        
        container.html(content);
        modal.show();
    }

    // Close modal
    $('.modal-overlay, .modal-close').on('click', function() {
        $('#mangora-player-modal').hide();
        $('#mangora-player-container').html('');
    });

    // Escape HTML helper
    function escapeHtml(text) {
        if (!text) return '';
        return text
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

})(jQuery);
