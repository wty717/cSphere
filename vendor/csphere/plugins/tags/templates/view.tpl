<div class="panel panel-default panel-body">

    {* tpl default/com_header plugin=tags action=default.view *}

    <br />

    <table class="table table-striped table-hover">
        <tr>
            <th>
                {* lang name *}
            </th>
            <td>
                {* var tags.tag_name *}
            </td>
        </tr>
        <tr>
            <th>
                {* lang since *}
            </th>
            <td>
                {* date tags.tag_since *}
            </td>
        </tr>
    </table><!--END table-->

</div><!--END panel-->