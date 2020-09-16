$(document).ready(function(){
    $('.switch').click(function(){
        $(this).toggleClass("switch--on");

        // grab element reference archived toggle
        const archivedListing = document.querySelector('.is--archived');
        archivedListing.classList.toggle('d-none');
    });
});

