<?php
function epfl_memento_display_example($rss_xml, $max_number)
{
  $ns = $rss_xml->channel->getNamespaces(true);
  $count = 0;
  echo "<table>";
  foreach ($rss_xml->channel->item as $item) {
    $epfl = $item->children($ns['epfl']);
    echo "<tr>";
    echo "<td width=30%>Title</td>";
    echo "<td>" . $item->title . "</td>";
    echo "</tr>";

    echo "<tr>";
    echo "<td>Start date</td>";
    echo "<td>" . $epfl->startDate . '</td>';
    echo "</tr>";

    echo "<tr>";
    echo "<td>End date</td>";
    echo "<td>" . $epfl->endDate . '</td>';
    echo "</tr>";

    echo "<tr>";
    echo "<td>Keywords</td>";
    echo "<td>" . $epfl->keywords . '</td>';
    echo "</tr>";

    echo "<tr>";
    echo "<td>Domains</td>";
    echo "<td>" . $epfl->domains . '</td>';
    echo "</tr>";

    echo "<tr>";
    echo "<td>Filter</td>";
    echo "<td>" . $epfl->filter . '</td>';
    echo "</tr>";

    echo "<tr>";
    echo "<td>Link</td>";
    echo "<td>" . $item->link . '</td>';
    echo "</tr>";

    echo "<tr>";
    echo "<td>Themes</td>";
    echo "<td>" . $epfl->themes . '</td>';
    echo "</tr>";

    echo "<tr>";
    echo "<td>Canceled</td>";
    echo "<td>" . $epfl->canceled . '</td>';
    echo "</tr>";

    echo "<tr>";
    echo "<td>Image</td>";
    echo "<td><img src='" . $epfl->image . "'</td>";
    echo "</tr>";

    echo "<tr>";
    echo "<td>URL Ref</td>";
    echo "<td>" . $epfl->urlRef . '</td>';
    echo "</tr>";

    echo "<tr>";
    echo "<td>Location</td>";
    echo "<td>" . $epfl->location . '</td>';
    echo "</tr>";

    echo "<tr>";
    echo "<td>URL Location</td>";
    echo "<td>" . $epfl->urlLocation . '</td>';
    echo "</tr>";

    echo "<tr>";
    echo "<td>Category</td>";
    echo "<td>" . $epfl->category . '</td>';
    echo "</tr>";

    echo "<tr>";
    echo "<td>Description</td>";
    echo "<td>" . $item->description . '</td>';
    echo "</tr>";

    echo "<tr>";
    echo "<td>Speaker</td>";
    echo "<td>" . $epfl->speaker . '</td>';
    echo "</tr>";

    echo "<tr>";
    echo "<td>Contact</td>";
    echo "<td>" . $epfl->contact . '</td>';
    echo "</tr>";

    echo "<td colspan=2><hr></td>";
    if ($count++ >= $max_number) break;
  }
  echo "</table>";
}
