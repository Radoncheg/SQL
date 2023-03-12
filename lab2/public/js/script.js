(function () {
    const table = document.querySelector('.ui-table')
    const reset = document.getElementById('reset')
    const number = document.querySelector('#pagination')

    table.addEventListener('click', (ev) => {
        const id = ev.target.id
        if (id) {
            const url = new URL(window.location)
            url.searchParams.set('sort_by_field', id)
            const sort = url.searchParams.get('sort_ascending')
            url.searchParams.set('sort_ascending', sort === 'true' ? 'false' : 'true')

            window.open(url, '_self')
        }
    })

    reset.addEventListener('click', (ev) => {
        const url = new URL(window.location);

        const params = ['filter_by_status', 'filter_by_depth', 'filter_by_product', 'search_query']
        for (let i = 0; i < params.length; i++) {
            url.searchParams.set(params[i], "");
        }
        document.getElementById('filter_by_product').innerHTML = "";
        document.getElementById('filter_by_depth').innerHTML = "";
        document.getElementById('filter_by_status').innerHTML = "";
        document.getElementById('search_query').innerHTML = "";

        window.open(url, '_self')
    })

    number.addEventListener('click', function(e){
        const id = e.target.id;
        if (id && id !== 'pagination') {
            const url = new URL(window.location)
            url.searchParams.set('page_no', id)

            window.open(url, '_self')
        }
    });

})()