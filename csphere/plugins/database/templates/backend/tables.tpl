<div class="panel panel-default">
    <div class="panel-body">

        <header>
            <section class="page-header">
                <h3>
                    {* lang database.database *} - {* lang database.tables *}
                    <small> - {* lang database.tables *}: {* var count *}</small>
                </h3><!--END header page-header headline-->
            </section><!--END header page-header-->
        </header><!--END header-->

        {* tpl default/msg_error *}

        <ul class="nav nav-tabs nav-justified" role="tablist">
            <li><a href="{* link database/control *}">{* lang default.control *}</a></li>
            <li class="active"><a href="{* link database/tables *}">{* lang database.tables *}</a></li>
        </ul><!--END nav-tabs-->

        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>{* lang default.plugin *}</th>
                    <th>{* lang database.table *}</th>
                    <th class="text-center">{* lang default.records *}</th>
                </tr>
            </thead><!--END table thead-->

            <tbody>
                {* foreach tables *}
                <tr>
                    <td>
                        <a href="{* link database/details/dir/$tables.plugin$ *}">{* raw tables.plugin *}</a>
                    </td>
                    <td>{* var tables.table *}</td>
                    <td class="text-center">{* var tables.records *}</td>
                </tr>
                {* endforeach tables *}
            </tbody><!--END table tbody-->
        </table><!--END table-->

    </div><!--END panel-body-->
</div><!--END panel-->
