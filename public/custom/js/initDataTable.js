function initReportTable() {

    const table = document.querySelector("#jobdesc-table");

    if (!table) {
        return;
    }

    if ($.fn.DataTable.isDataTable("#jobdesc-table")) {
        return;
    }


    fetch("/report/jobdesc-section")
        .then(response => response.json())
        .then(result => {


            // Generate header
            table.querySelector("thead").innerHTML = `
                <tr>
                    ${
                        result.columns.map(column => {
                            return `<th>${column.title}</th>`;
                        }).join("")
                    }
                </tr>
            `;



            new DataTable("#jobdesc-table", {


                data: result.data,


                columns: result.columns.map(column => {

                    if (column.data === "no") {

                        return {
                            data: null,
                            width: column.width,

                            className: "text-center",

                            orderable: false,
                            searchable: false,

                            render: function (data, type, row, meta) {

                                return meta.row + 1;

                            }
                        };

                    }


                    // ACTION COLUMN
                    if (column.data === null) {

                        return {

                            data: null,

                            width: column.width ?? "10%",
                            className: "text-center",

                            orderable: false,

                            searchable: false,


                            render: function (data, type, row) {

                                return `
                                    <button 
                                        type="button"
                                        class="btn btn-sm btn-light-success me-1"
                                        onclick="editWorkshop(${row.id})">

                                        <i class="ti ti-edit"></i>

                                    </button>


                                    <button 
                                        type="button"
                                        class="btn btn-sm btn-light-danger"
                                        onclick="deleteWorkshop(${row.id})">

                                        <i class="ti ti-trash"></i>

                                    </button>
                                `;
                            }
                        };

                    }



                    return {

                        data: column.data,

                        width: column.width ?? "20%"

                    };


                }),



                autoWidth: false,

                responsive: true,


                layout: {

                    topStart: "pageLength",

                    topEnd: "search",

                    bottomStart: "info",

                    bottomEnd: "paging"

                },


                pageLength: 10,

                searching: true,

                ordering: true,

                paging: true,

                info: true

            });


        })


        .catch(error => {

            console.error(
                "Load workshop table error:",
                error
            );

        });

}





const preview = document.querySelector(".content-preview");


if (preview) {


    const previewObserver = new MutationObserver(function () {


        const table = document.querySelector("#jobdesc-table");


        if (table) {

            initReportTable();

        }


    });



    previewObserver.observe(preview, {

        childList: true,

        subtree: true

    });

}