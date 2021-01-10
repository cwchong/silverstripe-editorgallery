<% if $Gallery.Photos %><ul class="gallery-thumbnail">
    <% loop $Gallery.Photos %><% if $Image %><li>
        <a href="$Image.URL" style="background:url($Image.URL)no-repeat center center" title="$Caption"></a>
    </li><% end_if %><% end_loop %>
</ul><% end_if %>