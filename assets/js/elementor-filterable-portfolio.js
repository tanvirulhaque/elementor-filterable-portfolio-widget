jQuery('document').ready(function (){
    jQuery("#efpw-load-more").on("click", function(e) {
        e.preventDefault();

        // var query = jQuery(this).attr("data-query");
        //     $button = jQuery(this);

        var $button = jQuery(this),
            query = $button.data('query');
        
        $button.text('Loading...');

        jQuery.ajax({
            type: "POST",
            url: loadMorePortfolio.ajaxurl,
            data: {
                action: "efpw_load_more_portfolio",
                query: query
            },

            success: function(response) {

                $button.text('Load More');

                if (response.data.posts) {

                    query.offset = response.data.query.query.offset;
                    
                    jQuery(".row.grid").append(response.data.posts);

                    $button.attr('data-query', query);

                } else {

                    $button.text('No More Portfolio');

                }
            },

            error: function (error) {

                $button.text('No More Portfolio');

            }

        });

    });
});