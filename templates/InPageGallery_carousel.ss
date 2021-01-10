<% if $Gallery.Photos %><div id="slider" class="flexslider gallery-slider with-caption">
     <ul class="slides">
        <% loop $Gallery.Photos %><% if $Image %><li>
            <img src="$Image.URL" alt="$Caption">
            <div class="photo-caption">$Caption</div>
        </li><% end_if %><% end_loop %>
    </ul>        
</div><% end_if %>