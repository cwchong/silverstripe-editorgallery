<% if $Timeline.Items %><div class="timeline-vertical">
    <ul class="sg-timeline">
        <% loop $Timeline.Items %><li>
            <div class="date">$Title</div>
            <div class="event">
                $Content
            </div>
            <div class="photo">$Image</div>
        </li><% end_loop %>
    </ul>
</div><% end_if %>