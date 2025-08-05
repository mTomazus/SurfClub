<div id="form-container" class="container text-center"></div>
<div id="load-on" mx-get="camps" 
                    mx-target="#form-container" 
                    mx-select="#nav-table"
                    mx-select-oob="#title:#top-title"
                    mx-trigger="load"></div>
<style>
    body {
        display:flex;
        flex-direction:row;
        background: purple;
        color: #000;
    }
    aside {
        position: sticky;
        top: 100px;
        width: 250px;
        height: calc(100vh - 100px);
    }
</style>