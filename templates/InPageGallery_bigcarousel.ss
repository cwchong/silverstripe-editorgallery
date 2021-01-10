<% if $Gallery.Photos %><div id="slider" class="flexslider big-slider" <% if $Gallery.Background %>style="background-image:url($Gallery.Background.URL);"<% end_if %>>
     <ul class="slides">
        <% loop $Gallery.Photos %><% if $Image %><li>
            <div class="image bc" id="bslider_$ID" ></div>
        </li><% end_if %><% end_loop %>
    </ul>        
</div>
<style>
.big-slider .slides li .image{padding-bottom: 36.484%;}
<% loop $Gallery.Photos %>
    <% if $Image %>#bslider_$ID { background-image:url($Image.URL); }<% end_if %>
<% end_loop %>
@media all and (max-width:768px){
    .big-slider .slides li .image{padding-bottom:100%;}
<% loop $Gallery.Photos %>
    <% if $ImageSmall %>#bslider_$ID { background-image:url($ImageSmall.URL); }<% end_if %>
<% end_loop %>
}
</style>
<% end_if %>