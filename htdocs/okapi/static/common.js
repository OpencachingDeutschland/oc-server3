$(function() {
    $('.issue-comments').each(function() {
        var div = $(this);
        var issue_id = div.attr('issue_id');
        $.ajax({
            type: 'GET',
            dataType: 'json',
            url: okapi_base_url + 'services/apiref/issue',
            data: {
                'issue_id': issue_id
            },
            success: function(issue)
            {
                var comments, link;
                if (issue.comment_count === null) {
                    link = $("<a>Comments</a>");
                } else {
                    comments = (issue.comment_count === 1) ? "comment" : "comments";
                    link = $("<a>" + issue.comment_count + " " + comments + "</a>");
                }
                link.attr('href', issue.url);
                div.append(link);
                var notice = $("<span class='notice'>Notice: comments are shared between all OKAPI installations.</span>");
                div.append(notice);
            }
        });
    });

    /**
     * When present on http(s)://opencaching.xx/okapi/some/path page, return "some/path".
     */
    var get_rel_path = function() {
        var myurl = window.location.href;
        var possible_prefixes = [
            okapi_base_url.replace("https://", "http://"),
            okapi_base_url.replace("http://", "https://")
        ];
        for (var i=0; i<possible_prefixes.length; i++) {
            var prefix = possible_prefixes[i];
            if (myurl.substr(0, prefix.length) === prefix) {
                return myurl.substr(prefix.length);
            }
        }
        throw "We're outside of okapi_base_url";
    };

    $('#switcher').change(function() {
        var current_base_url = $('#switcher option[current]').attr('value');
        var new_base_url = $('#switcher option:selected').attr('value');
        if (current_base_url !== new_base_url) {
            window.location.href = new_base_url + get_rel_path();
        }
    });
    $('#switcher option[current]').attr('selected', true);
});