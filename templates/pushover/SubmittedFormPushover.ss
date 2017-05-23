A Form has been submitted from the {$PageTitle} page.

<% loop $Fields %><% if Title %>$Title<% else %>$Name<% end_if %>: $FormattedValue
<% end_loop %>