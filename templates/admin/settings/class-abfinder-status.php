<h2>Online Database Status</h2>
<div class="abf-status-table">
    <table>
        <tbody>
            <tr>
                <th class="status-label">
                    Service
                </th>
                <td class="abf-status<?php echo $abfCodeStatus == 1 ? ' valid' : ($abfCodeStatus == 2 ? ' warning' : '') ?>">
                    <span> <?php echo $abfCodeStatus == 1 ? 'Available' : ($abfCodeStatus == 2 ? 'Expired' : 'Unavailable') ?> </span>
                </td>
            </tr>
            <tr>
                <th class="status-label">
                    Expiration
                </th>
                <td>
                    <span class="valid-date"> <?php echo esc_html($abfCodeExpiration); ?> </span>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<div id="site_notice"> </div>