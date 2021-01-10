<% if $Timeline.Items %><div class="timeline-horizontal">
    <h3>$Timeline.Name</h3>
    <div class="world-timeline-wrapper">
        <div class="owl-carousel owl-theme timeline">
            <% loop $Timeline.Items %><div class="item">
                <a <% if $First %>class="active"<% end_if %>>
                    <span class="dot"></span>
                    <span class="time">$Title</span>
                    <span class="time-ttl">$SubTitle</span>
                </a> 
            </div><% end_loop %>
        </div>
    </div>
    <div class="wrapper-inner">
       <div class="timeline-events owl-carousel owl-theme">
            <% loop $Timeline.Items %><div class="item">
                $Content
            </div><% end_loop %>
       </div>
    </div>
</div><% end_if %>