<script type="text/javascript"> 

function bookmarksite(title, url){
    if (document.all)
        window.external.AddFavorite(url, title);
    else if (window.sidebar)
        window.sidebar.addPanel(title, url, "");
}
</script>

<a href="javascript:bookmarksite('SHARK', 'http://localhost:2020')">SHARK!</a>