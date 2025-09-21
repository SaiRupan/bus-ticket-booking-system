$(document).ready(function() {
    $("#routesTable, #userTable, #usersTable").DataTable({
        paging: true,
        searching: true,
        ordering: true,
        order: [[0, 'asc']], // Default sorting on first column (S.No)
        lengthMenu: [10, 25, 50, 100],
        pageLength: 10,
        columnDefs: [
            { orderable: false, targets: -1 } // Disable ordering on the last column (Action)
        ]
    });
});
