<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    </head>
    <body>

    <div class="container">
        <div class="row">
            <div class="col-lg-1"></div>
            <div class="col-lg-10">
                <form>
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label for="name">Product name</label>
                        <input type="text" name="name" class="form-control" id="name" placeholder="">
                        <small></small>
                    </div>
                    <div class="form-group">
                        <label for="in_stock">Quantity in stock</label>
                        <input type="number" name="in_stock" class="form-control" id="in_stock" placeholder="">
                        <small></small>
                    </div>
                    <div class="form-group">
                        <label for="price"> Price per item</label>
                        <input type="number" name="price" class="form-control" id="price" placeholder="">
                        <small></small>
                    </div>
                    <button type="button" class="btn btn-success save_btn">Save</button>
                </form><br>
                <table class="table">
                    <thead class="thead-dark">
                    <tr>
                        <th scope="col">Product name</th>
                        <th scope="col">Quantity in stock</th>
                        <th scope="col">Price per item</th>
                        <th scope="col">Datetime submitted</th>
                        <th scope="col">Total value </th>
                    </tr>
                    </thead>
                    <tbody>
                    @if($data['information'])
                        @php($total = 0)
                        @foreach($data['information'] as $item)
                            <tr>
                                <th>{{ $item->name }}</th>
                                <td>{{ $item->in_stock }}</td>
                                <td>{{ $item->price }}</td>
                                <td>{{ $item->date }}</td>
                                <td class="row_total">{{ $item->total }}</td>
                            </tr>
                            @php($total = $total + $item->total)
                        @endforeach
                        <tr><td></td><td></td><td></td><td></td><th class="total">{{ $total }}</th></tr>
                    @endif
                    </tbody>
                </table>
            </div>
            <div class="col-lg-1"></div>
        </div>
    </div>

    <script
            src="https://code.jquery.com/jquery-3.4.0.js"
            integrity="sha256-DYZMCC8HTC+QDr5QNaIcfR7VSPtcISykd+6eSmBW5qo="
            crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $(".save_btn").click(function () {
                var save_btn = $(this);
                var form = $(this).closest('form');
                var name = $("#name").val();
                var in_stock = $("#in_stock").val();
                var price = $("#price").val();
                var token = $("input[name='_token']").val();

                $.ajax({
                    url : '{{ route('save') }}',
                    type: 'post',
                    data: { name: name, in_stock: in_stock, price: price, _token: token},
                    beforeSend: function () {
                        save_btn.prop('disabled',true);
                    },
                    error: function (err) {
                        $.each(form.find('input'), function (index,value) {
                            $('#'+index).next('small').text('');
                        });
                        if (typeof err.responseJSON != 'undefined') {
                            $.each(err.responseJSON, function (index,value) {
                                $('#'+index).next('small').text(value[0]);
                            });
                        }
                        save_btn.prop('disabled',false);
                    },
                    success: function (res) {
                        save_btn.prop('disabled',false);
                        var row_total = res.data.in_stock * res.data.price;
                        var html = '<tr>' +
                            '<th>'+ res.data.name +'</th>' +
                            '<td>'+ res.data.in_stock +'</td>' +
                            '<td>'+ res.data.price +'</td>' +
                            '<td>'+ res.data.date +'</td>' +
                            '<td class="row_total">'+ row_total +'</td>' +
                            '</tr>';
                        if ($('.total').length == 0) {
                            html = html.concat('<tr><td></td><td></td><td></td><td></td><th class="total">' + row_total + '</th></tr>');
                        }
                        $('table.table tbody').prepend(html);
                        var total = countTotal();

                        $('table.table .total').text(total);
                        $.each(form.find('input:not(input[name="_token"])'), function (index,value) {
                            $(value).val('');
                        });
                    }
                });
            });
        });
        
        function countTotal() {
            var total = 0;
            $.each($('table.table .row_total'), function (index, value) {
                total = total + parseInt($(value).text());
            })
            return total;
        }
    </script>
    </body>
</html>
