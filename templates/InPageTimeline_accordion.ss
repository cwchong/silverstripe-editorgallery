<% if $Timeline.Items %><ul class="accordion-list awards">
    <% loop $Timeline.Items %><li>
        <div class="acc-toggle">
            <div class="year">$Title</div>
            <div class="ttl">$SubTitle</div>
        </div>
        <div class="acc-content">
            $Content
        </div>
    </li><% end_loop %>
</ul><% end_if %>