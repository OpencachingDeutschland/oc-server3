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
					comments = (issue.comment_count == 1) ? "comment" : "comments";
					link = $("<a>" + issue.comment_count + " " + comments + "</a>");
				}
				link.attr('href', issue.url);
				div.append(link);
				var notice = $("<span class='notice'>Notice: comments are shared between all OKAPI installations.</span>");
				div.append(notice);
			}
		});
	});
	$('#switcher').change(function() {
		var current_base_url = $('#switcher option[current]').attr('value');
		var new_base_url = $('#switcher option:selected').attr('value');
		if (current_base_url != new_base_url)
			window.location.href = window.location.href.replace(current_base_url, new_base_url);
	});
	$('#switcher option[current]').attr('selected', true);
});