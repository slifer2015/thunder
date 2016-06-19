/**
 * Created by Netplorer on 7/7/14.
 */

$(function(){

    // ajax start spinner visible and ajax stop spinner hidden
    $(document).ajaxStart(function(){
        $('.spinnerContainer').fadeIn('fast');
    }).ajaxStop(function(){
        $('.spinnerContainer').fadeOut('fast');
    });

    // DATE PICKER
    var $body = $('body');

    $body.on('focus','[data-input="datepicker"]',function(){
        var $this = $(this),
            format = ($this.attr('data-format') === undefined) ? 'yyyy-mm-dd' : $this.attr('data-format'),
            startView = ($this.attr('data-view') === undefined) ? 0 : parseInt($this.attr('data-view'));

        $this.datepicker({
            format: format,
            startView: startView
        });
    });
    // END DATE PICKER

    $('[data-form="sale2wizard"] > [data-wizard]').steps({
        headerTag: "h3",
        bodyTag: "section",
        autoFocus: true,
        titleTemplate: '<span class="number"></span> #title#',

        /* Labels */
        labels: {
            finish: "اتمام",
            next: "بعدی",
            previous: "قبلی"
        },
        onStepChanging: function (event, currentIndex, newIndex){
            var form = $(this).parent();

            // Allways allow step back to the previous step even if the current step is not valid!
            if (currentIndex > newIndex)
            {
                return true;
            }

            if (currentIndex == 0) {
                $('input[name=customerId]').val($('#customer_id_holder').attr("data-id"));
            }

            form.validate().settings.ignore = ":disabled,:hidden";
            return form.valid();
        }
    });
    // END FORM WIZARD DEMO

    // FORM WIZARD DEMO
    $('[data-form="wizardInventory"] > [data-wizard]').steps({
        headerTag: "h3",
        bodyTag: "section",
        autoFocus: true,
        titleTemplate: '<span class="number"></span> #title#',

        /* Labels */
        labels: {
            finish: "اتمام",
            next: "بعدی",
            previous: "قبلی"
        },
        onStepChanging: function (event, currentIndex, newIndex){
            var form = $(this).parent();

            // Allways allow step back to the previous step even if the current step is not valid!
            if (currentIndex > newIndex)
            {
                if (currentIndex == 2 && newIndex == 1) {
                    $(".machinesPriceSection div").remove();
                    $(".deviceListRowsHolderInventory tr").remove();
                }

                return true;
            }

            if(currentIndex == 0)  {

                // Write on keyup event of keyword input element
                $("#search").keyup(function(){
                    var $_this = $(this);
                    // Show only matching TR, hide rest of them
                    $.each($("table.table.tableInventory tbody").find("tr"), function() {
                        console.log($(this).text());
                        if($(this).text().toLowerCase().indexOf($_this.val().toLowerCase()) == -1)
                            $(this).hide();
                        else
                            $(this).show();
                    });
                });

                if ($('input[name=field_saleTable_user]').val() == '') {
                    $.bootstrapGrowl("لطفا مشتری را به درستی انتخاب نمایید !", {
                        type: 'danger',
                        align: 'center',
                        width: 'auto',
                        allow_dismiss: true
                    });
                    return false;
                }
            }

            if(currentIndex == 1)  {

                var lengthChecked = $("#machineTableSaleInventory input:checked").length;
                if ((lengthChecked == 0)) {
                    $.bootstrapGrowl("حداقل باید یکی از دستگاه ها را انتخاب نمایید !", {
                        type: 'danger',
                        align: 'center',
                        width: 'auto',
                        allow_dismiss: true
                    });
                    return false;
                } else if (lengthChecked > 0) {

                    var totalPrice = 0;
                    var counter = 1;
                    $("#machineTableSaleInventory input:checked").each(function(){
                        totalPrice = parseInt(totalPrice) + parseInt($(this).attr("data-price"));

                        var appendElement = '<div class="row">'
                            +'<div class="col-xs-12 col-sm-12 col-md-4">'
                            +'<div class="form-group">'
                            +'<label for="calculated-price'+ counter +'" class="col-sm-5 control-label rtl pull-right text-10 text-normal">' + 'قیمت لیست ' + counter + '(' + $(this).parents('tr').find("td:nth-child(5)").text() + '):</label>'
                            +'<div class="col-sm-7 pull-right">'
                            +'<div class="input-group input-group-in">'
                            +'<span class="input-group-addon text-silver"><i class="fa fa-money"></i></span>'
                            +'<input type="text" name="calculated-price[]" id="calculated-price'+ counter +'" value="'+accounting.formatNumber(parseInt($(this).attr("data-price")))+'" class="form-control rtl" readonly>'
                            +'</div>'
                            +'</div>'
                            +'</div>'
                            +'</div>'

                            +'<div class="col-xs-12 col-sm-12 col-md-4">'
                            +'<div class="form-group">'
                            +'<label for="discountMachines'+ counter +'" class="col-sm-5 control-label rtl pull-right text-10 text-normal">' + 'تخفیف ' + counter + ' :</label>'
                            +'<div class="col-sm-7">'
                            +'<div class="input-group input-group-in">'
                            +'<span class="input-group-addon text-silver"><i class="fa fa-pencil"></i></span>'
                            +'<input type="text" class="form-control rtl" name="discountMachines[]" id="discountMachines'+ counter +'" value="0" readonly>'
                            +'</div>'
                            +'</div>'
                            +'</div>'
                            +'</div>'

                            +'<div class="col-xs-12 col-sm-12 col-md-4">'
                            +'<div class="form-group">'
                            +'<label for="priceMachines' + counter + '" class="col-sm-5 control-label rtl pull-right text-10 text-normal">' + 'قیمت فروش ماشین ' + counter + ' :</label>'
                            +'<div class="col-sm-7 pull-right">'
                            +'<div class="input-group input-group-in">'
                            +'<span class="input-group-addon text-silver"><i class="fa fa-money"></i></span>'
                            +'<input type="text" name="priceMachines[]" id="priceMachines' + counter + '" value="'+parseInt($(this).attr("data-price"))+'" class="form-control rtl">'
                            +'</div>'
                            +'</div>'
                            +'</div>'
                            +'</div>'
                            +'</div>';

                        counter = counter + 1;
                        $(".machinesPriceSection").append(appendElement);

                    });

                    $("#calculated-price").val(accounting.formatNumber(totalPrice) + " " + "ریال");
                    $("#ovlCalculatedPrice").text(accounting.formatNumber(totalPrice) + " " + "ریال");

                    var status = [];
                    $("#machineTableSaleInventory input:checked").each(function(){
                        status.push($(this).parents('tr').find("td:nth-child(3)").text());
                    });
                    $("input[name=machineStatus]").val(status);

                    var order = [];
                    $("#machineTableSaleInventory input:checked").each(function(){
                        order.push($(this).parents('tr').find("td:nth-child(4)").text());
                    });
                    $("input[name=machineOrder]").val(order);

                    var machineName = [];
                    $("#machineTableSaleInventory input:checked").each(function(){
                        machineName.push($(this).parents('tr').find("td:nth-child(5)").text());
                    });
                    $("input[name=machineName]").val(machineName);

                    var  totalPrice = 0;
                    var machinePrice = [];
                    $("#machineTableSaleInventory input:checked").each(function(){
                        totalPrice = totalPrice + accounting.unformat($(this).parents('tr').find("td:nth-child(6)").text());
                        machinePrice.push(accounting.unformat($(this).parents('tr').find("td:nth-child(6)").text()));
                    });

                    $("input[name=machinePrice]").val(machinePrice);
                    $("#calculated-price").val(accounting.formatNumber(totalPrice)+ " " + "ریال");
                    $("#field_price").val(totalPrice);

                    var machineLocation = [];
                    $("#machineTableSaleInventory input:checked").each(function(){
                        machineLocation.push($(this).parents('tr').find("td:nth-child(7)").text());
                    });
                    $("input[name=machineLocation]").val(machineLocation);

                    var arrivalMachine = [];
                    $("#machineTableSaleInventory input:checked").each(function(){
                        arrivalMachine.push($(this).parents('tr').find("td:nth-child(8)").text());
                    });
                    $("input[name=arrivalMachine]").val(arrivalMachine);

                    var productId = [];
                    $("#machineTableSaleInventory input:checked").each(function(){
                        productId.push($(this).val());
                    });
                    $("input[name=field_saleTable_productId]").val(productId);

                    var orderDetail = [];
                    $("#machineTableSaleInventory input:checked").each(function(){
                        orderDetail.push($(this).attr("data-id"));
                    });
                    $("input[name=orderDetailId]").val(orderDetail);

                    var rowHolder = $(".deviceListRowsHolderInventory");
                    var lengthArray = status.length;
                    var rowsStream = "";
                    var counter = 1;

                    for (var i = 0; i < lengthArray; i++) {

                        rowsStream += "<tr>";
                        rowsStream += "<td class='text-center'>" + counter +"</td>";
                        rowsStream += "<td class='text-center'>" + status[i] + "</td>";
                        rowsStream += "<td class='text-center'>" + order[i] + "</td>";
                        rowsStream += "<td class='text-center'>" + machineName[i] + "</td>";
                        rowsStream += "<td class='text-center'>" + accounting.formatNumber(machinePrice[i]) + "</td>";
                        rowsStream += "<td class='text-center'></td>";
                        rowsStream += "<td class='text-center'>" + machineLocation[i] + "</td>";
                        rowsStream += "<td class='text-center'>" + arrivalMachine[i] +"</td>";
                        rowsStream += "</tr>";

                        counter++;
                    }

                    rowHolder.append(rowsStream);
                }

                // check which input have been changed
                $(".machinesPriceSection input[name='priceMachines[]']").each(function() {
                    $(this).bind('keydown keyup keypress focus blur paste change', function() {

                        var calculateVal = accounting.unformat($(this).closest('.row').find("input[name='calculated-price[]']").val());
                        var discountPrice = calculateVal - $(this).val();
                        $(this).closest('.row').find("input[name='discountMachines[]']").val(accounting.formatNumber(discountPrice));

                        updateTotal();
                    });
                });

                var updateTotal = function() {
                    var totalMachinePrice = null;
                    $(".machinesPriceSection input[name='priceMachines[]']").each(function() {
                        totalMachinePrice = totalMachinePrice + parseInt($(this).val());
                    });

                    $("#field_price").val(totalMachinePrice);
                };
            }

            if (currentIndex == 2) {


                var machineSalePrice = [];
                $(".machinesPriceSection input[name='priceMachines[]']").each(function() {
                    machineSalePrice.push($(this).val());
                });

                var lengthArray = machineSalePrice.length;
                for (var j = 0; j < lengthArray; j++) {
                    $('.deviceListRowsHolderInventory tr:eq('+j+') td:nth-child(6)').text(accounting.formatNumber(machineSalePrice[j]));
                }

                $("#ovlDueDate").text($("#field_deliveryDate").val());
                $("#ovlRemain").text(accounting.unformat($('#saleIndicator_').text())  + " ریال");

                $('input[name=field_saleTable_discount]').val(accounting.unformat($("#fields_discount").val()));
                $('input[name=field_saleTable_soldPrice]').val(accounting.unformat($('#salePrice').text()));
                $('input[name=field_saleTable_remainPrice]').val(accounting.unformat($('#saleIndicator_').text()));
                $('input[name=field_saleTable_totalPrice]').val(accounting.unformat($('#totalAmount').text()));
                $('input[name=field_saleTable_soldType]').val($('#saleType').val());
                $('input[name=field_saleTable_vat6]').val(accounting.unformat($('#vat').text()));
                $('input[name=field_saleTable_downPayment]').val($('#field_down_payment').val());
                $('input[name=field_saleTable_installmentNo]').val($('#field_installment').val());
                $('input[name=field_saleTable_creationDate]').val($('#field_deliveryDate').val());

                ////
                var  calculatedPrice = '';
                $(".machinesPriceSection input[name='calculated-price[]']").each(function() {
                    calculatedPrice = calculatedPrice + accounting.unformat($(this).val()) + ",";
                });

                var  discountMachines = '';
                $(".machinesPriceSection input[name='discountMachines[]']").each(function() {
                    discountMachines = discountMachines + accounting.unformat($(this).val()) + ",";
                });

                var  priceMachines = '';
                $(".machinesPriceSection input[name='priceMachines[]']").each(function() {
                    priceMachines = priceMachines + $(this).val() + ",";
                });

                $('input[name=field_saleTable_priceMachines]').val(priceMachines);
                $('input[name=field_saleTable_discountMachines]').val(discountMachines);
                $('input[name=field_saleTable_calculatedPriceMachines]').val(calculatedPrice);
                ////

                var contractNum = $("#contract_number");
                var communication = $("#communication_method");

                $("input[name=field_contract_number]").val(contractNum.val());
                $("input[name=field_communication_method]").val(communication.val());
                $("#contractNumberOve").text(contractNum.val());
                $("#communicationMethodOve").text(communication.val());

                $(".registerDoc tbody tr td:last-child").remove();
                $(".registerDoc thead tr th:last-child").remove();

                var activeBtn = true;
                $("input.require").each(function(){
                    if ($(this).val() == '') {
                        activeBtn = false;
                    }
                });

                if (activeBtn == true) {
                    $("#btnSend2Archive").parent().css("display","block");
                }
            }

            form.validate().settings.ignore = ":disabled,:hidden";
            return form.valid();
        }
    });
    // END FORM WIZARD DEMO

    $('[data-form="wizardSale"] > [data-wizard]').steps({
        headerTag: "h3",
        bodyTag: "section",
        autoFocus: true,
        titleTemplate: '<span class="number"></span> #title#',

        /* Labels */
        labels: {
            finish: "اتمام",
            next: "بعدی",
            previous: "قبلی"
        },

        onStepChanging: function (event, currentIndex, newIndex){
            var form = $(this).parent();

            // Allways allow step back to the previous step even if the current step is not valid!
            if (currentIndex > newIndex)
            {
                if (currentIndex == 2 && newIndex == 1) {
                    $(".machinesPriceSection div").remove();
                }

                return true;
            }

            if (currentIndex == 0) {

                if ($('input[name=field_saleTable_user]').val() == '') {
                    $.bootstrapGrowl("لطفا مشتری را به درستی انتخاب نمایید !", {
                        type: 'danger',
                        align: 'center',
                        width: 'auto',
                        allow_dismiss: true
                    });
                    return false;
                }

            }

            if(currentIndex == 1)  {
                var checkStep = $(".deviceListRowsHolder tr").length;
                if ((checkStep == 0)) {
                    $.bootstrapGrowl("لطفا حداقل یک ماشین را به لیست اضافه کنید.", {
                        type: 'danger',
                        align: 'center',
                        width: 'auto',
                        allow_dismiss: true
                    });
                    return false;

                } else {

                    var productId = '';
                    $('.deviceListRowsHolder tr').each(function(){
                        productId = productId + "*" + $(this).attr("data-id");
                    });

                    productId = productId.substring(1);

                    var counter = 1;

                    $.ajax({
                        url: 'sale.php?action=calculatePriceMachines',
                        type: 'post',
                        dataType: 'json',
                        data: {
                            productId: productId
                        },
                        success: function (data) {

                            //var temp = jQuery.parseJSON(data);

                            console.log(data);

                            $.each(data, function (key, value) {

                                var appendElement = '<div class="row">'
                                    +'<div class="col-xs-12 col-sm-12 col-md-4">'
                                    +'<div class="form-group">'
                                    +'<label for="calculated-price'+ counter +'" class="col-sm-5 control-label rtl pull-right text-10 text-normal">' + 'قیمت لیست ' + counter + '(' + value.machineName + '):</label>'
                                    +'<div class="col-sm-7 pull-right">'
                                    +'<div class="input-group input-group-in">'
                                    +'<span class="input-group-addon text-silver"><i class="fa fa-money"></i></span>'
                                    +'<input type="text" name="calculated-price[]" id="calculated-price'+ counter +'" value="'+accounting.formatNumber(value.price)+'" class="form-control rtl" readonly>'
                                    +'</div>'
                                    +'</div>'
                                    +'</div>'
                                    +'</div>'

                                    +'<div class="col-xs-12 col-sm-12 col-md-4">'
                                    +'<div class="form-group">'
                                    +'<label for="discountMachines'+ counter +'" class="col-sm-5 control-label rtl pull-right text-10 text-normal">' + 'تخفیف ' + counter + ' :</label>'
                                    +'<div class="col-sm-7">'
                                    +'<div class="input-group input-group-in">'
                                    +'<span class="input-group-addon text-silver"><i class="fa fa-pencil"></i></span>'
                                    +'<input type="text" class="form-control rtl" name="discountMachines[]" id="discountMachines'+ counter +'" value="0" readonly>'
                                    +'</div>'
                                    +'</div>'
                                    +'</div>'
                                    +'</div>'

                                    +'<div class="col-xs-12 col-sm-12 col-md-4">'
                                    +'<div class="form-group">'
                                    +'<label for="priceMachines' + counter + '" class="col-sm-5 control-label rtl pull-right text-10 text-normal">' + 'قیمت فروش ماشین ' + counter + ' :</label>'
                                    +'<div class="col-sm-7 pull-right">'
                                    +'<div class="input-group input-group-in">'
                                    +'<span class="input-group-addon text-silver"><i class="fa fa-money"></i></span>'
                                    +'<input type="text" name="priceMachines[]" id="priceMachines' + counter + '" value="'+value.price+'" class="form-control rtl">'
                                    +'</div>'
                                    +'</div>'
                                    +'</div>'
                                    +'</div>'
                                    +'</div>';

                                counter = counter + 1;
                                $(".machinesPriceSection").append(appendElement);

                            });
                        }
                    });

                    $("input[name=field_saleTable_productId]").val(productId);

                    $.ajax({
                        url: 'sale.php?action=calculatePrice',
                        type: 'post',
                        dataType: 'json',
                        data: {
                            productId: productId
                        },
                        success: function (data) {
                            $("#calculated-price").val(accounting.formatNumber(data));
                            $("#field_price").val(data);
                            $("#ovlCalculatedPrice").text(accounting.formatNumber(data)+ " " + 'ریال');
                            $("#fields_discount").val(0);
                        }
                    });
                }
            }

            if (currentIndex == 2) {
                $("#ovlDueDate").text($("#field_deliveryDate").val());
                $("#ovlRemain").text(accounting.unformat($('#saleIndicator_').text())  + " ریال");

                $('input[name=field_saleTable_discount]').val(accounting.unformat($("#fields_discount").val()));
                $('input[name=field_saleTable_soldPrice]').val(accounting.unformat($('#salePrice').text()));
                $('input[name=field_saleTable_remainPrice]').val(accounting.unformat($('#saleIndicator_').text()));
                $('input[name=field_saleTable_totalPrice]').val(accounting.unformat($('#totalAmount').text()));
                $('input[name=field_saleTable_soldType]').val($('#saleType').val());
                $('input[name=field_saleTable_vat6]').val(accounting.unformat($('#vat').text()));
                $('input[name=field_saleTable_downPayment]').val($('#field_down_payment').val());
                $('input[name=field_saleTable_installmentNo]').val($('#field_installment').val());
                $('input[name=field_saleTable_creationDate]').val($('#field_deliveryDate').val());

                ////
                var  calculatedPrice = '';
                $(".machinesPriceSection input[name='calculated-price[]']").each(function() {
                     calculatedPrice = calculatedPrice + accounting.unformat($(this).val()) + ",";
                });

                var  discountMachines = '';
                $(".machinesPriceSection input[name='discountMachines[]']").each(function() {
                     discountMachines = discountMachines + accounting.unformat($(this).val()) + ",";
                });

                var  priceMachines = '';
                $(".machinesPriceSection input[name='priceMachines[]']").each(function() {
                    priceMachines = priceMachines + $(this).val() + ",";
                });

                $('input[name=field_saleTable_priceMachines]').val(priceMachines);
                $('input[name=field_saleTable_discountMachines]').val(discountMachines);
                $('input[name=field_saleTable_calculatedPriceMachines]').val(calculatedPrice);
                ////

                var contractNum = $("#contract_number");
                var communication = $("#communication_method");

                $("input[name=field_contract_number]").val(contractNum.val());
                $("input[name=field_communication_method]").val(communication.val());
                $("#contractNumberOve").text(contractNum.val());
                $("#communicationMethodOve").text(communication.val());

                var activeBtn = true;
                $("input.require").each(function(){
                    if ($(this).val() == '') {
                        activeBtn = false;
                    }
                });

                if (activeBtn == true) {
                    $("#btnSend2Archive").parent().css("display","block");
                }

                //$(".registerDoc tbody tr td:nth-child(1)").remove();
            }

            form.validate().settings.ignore = ":disabled,:hidden";
            return form.valid();
        }
    });


    $(document).ajaxStop(function(){

        // check which input have been changed
        $(".machinesPriceSection input[name='priceMachines[]']").each(function() {
            $(this).bind('keydown keyup keypress focus blur paste change', function() {

                var calculateVal = accounting.unformat($(this).closest('.row').find("input[name='calculated-price[]']").val());
                var discountPrice = calculateVal - $(this).val();
                $(this).closest('.row').find("input[name='discountMachines[]']").val(accounting.formatNumber(discountPrice));

                updateTotal();
            });
        });

        var updateTotal = function() {
            var totalMachinePrice = null;
            $(".machinesPriceSection input[name='priceMachines[]']").each(function() {
                totalMachinePrice = totalMachinePrice + parseInt($(this).val());
            });

            $("#field_price").val(totalMachinePrice);
        };

    });

    /* --------------------------------------------------------- */
    /* ---------------------    Order    ----------------------- */
    /* --------------------------------------------------------- */

    // END FORM WIZARD DEMO
    $('#orderFrm[data-form="wizardOrder"] > [data-wizard]').steps({
        headerTag: "h3",
        bodyTag: "section",
        autoFocus: true,
        titleTemplate: '<span class="number"></span> #title#',

        /* Labels */
        labels: {
            finish: "اتمام",
            next: "بعدی",
            previous: "قبلی"
        },
        onStepChanging: function (event, currentIndex, newIndex){
            var form = $(this).parent(),
                $orderID = $('#orderID');

            // Allways allow step back to the previous step even if the current step is not valid!
            if (currentIndex > newIndex)
            {
                return true;
            }

            if(currentIndex == 0)  {

                // error when user did not add any machine to order
                if($('#machine tbody tr').length == 0) {
                    $.bootstrapGrowl("حداقل یک ماشین انتخاب نمایید !" , {
                        type: 'danger',
                        align: 'center',
                        width: 'auto',
                        allow_dismiss: true
                    });
                    return false;
                } else {

                    var machines = $('#machine').html(),
                        text = $('#summernote').code(),
                        append =  text + "<br><br>" + machines;

                    $('#summernote').html(append);

                    if ($("#pageType").val() == 'orderSpecial') {
                        $('#summernote thead tr th:nth-child(1),#summernote tbody tr td:nth-child(1)').remove();
                    }

                    $('#summernote').summernote({
                        height: 200,
                        minHeight: 200,             // set maximum height of editor
                        maxHeight: 300,             // set maximum height of editor
                        toolbar: [
                            //[groupname, [button list]]

                            ['style', ['bold', 'italic', 'underline', 'clear']],
                            ['font', ['strikethrough']],
                            ['fontsize', ['fontsize']],
                            ['color', ['color']],
                            ['para', ['ul', 'ol', 'paragraph']],
                            ['height', ['height']]
                        ]
                    });

                    var productId = '';
                    $('#machine table tbody tr').each(function(){
                        productId = productId + "*" + $(this).attr("data-id");
                    });
                    productId = productId.substring(1);

                    $("#productID").val(productId);

                }

            } else if(currentIndex == 1)  {

                var orderName = $("#orderName");

                if ((orderName.val()=='')) {

                    if (orderName.val() == '') {
                        orderName.css("border-color","crimson");
                    } else {
                        orderName.css("border-color","#DFE3E7");
                    }

                    $.bootstrapGrowl("فیلد نام سفارش ضروری است !", {
                        type: 'danger',
                        align: 'center',
                        width: 'auto',
                        allow_dismiss: true
                    });

                    return false;

                } else {
                    var data = $('#orderFrm').serialize();
                    console.log(data);
                    if($orderID.val() != "0") {
                        $.bootstrapGrowl("اطلاعات در حال ویرایش می باشد، لطفا صبر کنید...", {
                            type: 'warning',
                            align: 'center',
                            width: 'auto',
                            allow_dismiss: false
                        });
                    } else {
                        $.bootstrapGrowl("اطلاعات در حال ثبت شدن می باشد، لطفا صبر کنید...", {
                            type: 'warning',
                            align: 'center',
                            width: 'auto',
                            allow_dismiss: false
                        });
                    }

                    $.post("order.php",data,function(result){
                        var resultID = JSON.parse(result);
                        $orderID.val(resultID.orderID);
                        setTimeout(function(){
                            $.bootstrapGrowl("اطلاعات با موفقیت ثبت شد.", {
                                type: 'success',
                                align: 'center',
                                width: 'auto',
                                allow_dismiss: false
                            });
                        },4000);
                    });
                }

            }

            form.validate().settings.ignore = ":disabled,:hidden";
            return form.valid();
        }
    });
    // END FORM WIZARD DEMO

    $('#create_pdf').bind('click',function(e){
        e.preventDefault();
        var text = $('#summernote').code();

        $.post('order.php',{action:'create_pdf',htmlStream:text,orderID:$('#orderID').val()},function(result){
            if(result == 1) {
                $.bootstrapGrowl("PDF مورد نظر با موفقیت ساخته شد", {
                    type: 'success',
                    align: 'center',
                    width: 'auto',
                    allow_dismiss: false
                });

                var orderID = $('#orderID').val(),
                    root = $('#RELA_DIR').val();


                $('#pdfContainer a').attr("href",root + orderID + ".pdf");

                $('#pdfContainer').removeClass('hidden');

            } else {
                $.bootstrapGrowl("درخواست شما امکان پذیر نمی باشد لطفا با مدیر سایت تماس حاصل فرمایید.", {
                    type: 'danger',
                    align: 'center',
                    width: 'auto',
                    allow_dismiss: false
                });
            }
        });
    });

    $('[data-input="selectboxit"], .selectboxit').each(function(){
        var $this = $(this),
            placeholder = ($this.attr('placeholder') === undefined) ? 'Select a choice' : $this.attr('placeholder'),
            downArrowIcon = ($this.attr('data-arrow') === undefined) ? '' : $this.attr('data-arrow'),
            native = ($this.attr('data-native') === undefined) ? false : true;

        $this.selectBoxIt({
            defaultText: placeholder,
            downArrowIcon: downArrowIcon,
            native: native
        });
    });

    $('#paymentMethod').change(function() {
        if($('#paymentMethod').val() == '1') {
            $('#lcPart').removeClass("hide");
            $('#cashPart').addClass("hide");
        } else {
            $('#lcPart').addClass("hide");
            $('#cashPart').removeClass("hide");
        }
    });

    var $installmentCount = $('#installmentCount'),
        counter = 2,
        maxCashNumber = 10;

    $installmentCount.val($('.paymentMethod input[name="pay[]"]').length);

    $body.on('click','a[data-role="remove"]',function(e){
        e.preventDefault();
        if(counter >= 2) {
            counter--;
            $('.paymentMethod .row[data-target="'+counter+'"]').remove();
            $installmentCount.val($('.paymentMethod input[name="pay[]"]').length);
        }
    });

    $('a[data-role="add"]').bind('click',function(e){
        e.preventDefault();
        if(counter <= maxCashNumber) {
            if(counter <= 2) {
                counter = 2;
            }
            var htmlStream = '';
            htmlStream += '<div class="row" data-target="'+counter+'">';
            htmlStream +=   '<div class="col-md-12">';
            htmlStream +=       '<div class="form-group">';
            htmlStream +=           '<label for="pa'+counter+'" class="col-sm-5 control-label rtl pull-right text-18 text-normal pull-right">مقدار پرداخت '+counter+'</label>';
            htmlStream +=           '<div class="col-sm-6 pull-right">';
            htmlStream +=               '<input type="text" name="pay[]" id="pa'+counter+'" class="form-control numeric">';
            htmlStream +=           '</div>';
            htmlStream +=       '</div>';
            htmlStream +=   '</div>';
            htmlStream += '</div>';
            htmlStream += '<div class="row" data-target="'+counter+'">';
            htmlStream +=   '<div class="col-md-12">';
            htmlStream +=       '<div class="form-group pull-right">';
            htmlStream +=           '<label for="pt'+counter+'" class="col-sm-5 control-label rtl pull-right text-18 text-normal pull-right">تاریخ پرداخت '+counter+'</label>';
            htmlStream +=           '<div class="col-sm-6 pull-right">';
            htmlStream +=               '<div class="input-group input-group-in">';
            htmlStream +=                   '<span class="input-group-addon text-silver"><i class="fa fa-calendar"></i></span>';
            htmlStream +=                   '<input type="text" name="payDate[]" id="pt'+counter+'" data-input="datepicker" data-view="2" class="form-control ltr">';
            htmlStream +=               '</div>';
            htmlStream +=           '</div>';
            htmlStream +=       '</div>';
            htmlStream +=   '</div>';
            htmlStream += '</div>';
            htmlStream += '<div class="row xsmallSpace" data-target="'+counter+'" ></div>';

            $(htmlStream).appendTo('.paymentMethod');
            counter++;
            $installmentCount.val($('.paymentMethod input[name="pay[]"]').length);
        }
    });


    $('.cancelOrder').bind("click",function(e){
        e.preventDefault();
        $('#cancelOrderId').val($(this).attr('data-id'));
        $('#cancelOrderModal').modal("show");
    });

    $('.deleteCustomer').bind("click",function(){
        $('#customerId').val($(this).attr('data-id'));
        $('#deleteCustomerModal').modal("show");
    });

    $('.deleteCompany').bind("click",function(){
        $('#companyId').val($(this).attr('data-id'));
        $('#deleteCompanyModal').modal("show");
    });

    $('.glyphicon-comment').bind("click",function(){
        $('#orderDetailId').val($(this).attr('data-id'));
        $('#addDescriptionToMachine').modal("show");
    });

    $('#birthday').persianDatepicker();
    $('#field_deliveryDate').persianDatepicker();
    $('#deliveryTime').persianDatepicker();


    $("#btnSelectCustomer").click(function() {
        $("#customer_id_holder").val($("input[name='customer_id']:checked").attr("data-name"));
        $("#customer_id_holder").attr("data-id",$("input[name='customer_id']:checked").val());
        $('input[name=field_saleTable_user]').val($('#customer_id_holder').attr("data-id"));
        $("#ovlCustomer").text($("input[name='customer_id']:checked").attr("data-name"));
        $("#customerList").modal("hide");
    });


    // reset special form of machines
    var refreshInputs = function() {
        // clear and select first main drive option
        $('input[type="radio"]:eq(1)').prop("checked",true);

        // clear checkbox selected and select first default option
        $('input[type="checkbox"]').prop("checked",false);

        // clear options selected
        $('select option:nth-child(1)').prop("selected",true);
        $('#machine_type option:nth-child(2)').prop("selected",true);
        $('#screw_type option:nth-child(2)').prop("selected",true);

        // empty value of quantity
        $('#quantity').val("");

        $("#screw_size option").remove();

        var rowsStream = '';
        rowsStream += '<option value = "">' + 'انتخاب' + '</option>';

        $("#screw_size").append(rowsStream);

        var selectVal = $("#machine_type option:eq(1)").val();
        // options
        $('#deviceOptions input[type="checkbox"]').each(function(){
            if ($(this).attr("data-model") == selectVal) {
                $(this).prop("checked",true);
            }
        });
    };

    // get value elements of special form into table
    var refreshDeviceTable = function() {
        var rowsHolder = $(".deviceListRowsHolder");
        var rowsHolder1 = $(".deviceListRowsHolder1");
        rowsHolder.html("");
        rowsHolder1.html("");
        if (deviceArray.length == 0) {
            rowsHolder.html("");
            rowsHolder.parent().addClass('hide');
            return false;
        }else{
            rowsHolder.parent().removeClass('hide');
        }
        if (deviceArray.length == 0) {
            rowsHolder1.html("");
            rowsHolder1.parent().addClass('hide');
            return false;
        }else{
            rowsHolder1.parent().removeClass('hide');
        }

        var rowsStream = "";
        var rowsStream1 = "";

        for (var i = 0,j = 0; i < deviceArray.length; i++,j++) {
            var device = deviceArray[i];

            rowsStream += '<tr data-id="'+ device.id +'" data-screw="'+device.screw_size+'">';
            rowsStream += '<td class="text-center">' + (i+1) +'</td>';
            rowsStream += '<td class="text-center">' + device.main_drive + " " + device.clamping + " " + device.machine_type + '</td>';
            rowsStream += '<td class="text-center">' + device.screw_type + '</td>';
            rowsStream += '<td class="text-center">' + device.screw_size + '</td>';
            rowsStream += '<td class="text-center">' + device.quantity +'</td>';
            rowsStream += '<td class="text-center">' + device.optionsPs +'</td>';
            rowsStream += '<td class="text-center"><a data-reserve-id="'+ device.id +'" class="btn-reserveDevice" data-id="' + i + '" href="#"><i class="glyphicon glyphicon-ok"></i></a><span class="seperator">|</span><a class="btn-editDevice" data-edit-id="'+ device.id +'" data-id="' + (i) + '" href="#"><i class="glyphicon glyphicon-pencil"></i></a><span class="seperator"> | </span><a data-delete-id="'+ device.id +'" class="btn-delete" data-id="' + (i) + '" href="#"><i class="glyphicon glyphicon-trash"></i></a></td>';
            rowsStream += '</tr>';

            rowsStream1 += '<tr data-id="'+ device.id +'">';
            rowsStream1 += '<td class="text-center">' + (j+1) +'</td>';
            rowsStream1 += '<td class="text-center">' + device.main_drive + " " + device.clamping + " " + device.machine_type + '</td>';
            rowsStream1 += '<td class="text-center">' + device.screw_type + '</td>';
            rowsStream1 += '<td class="text-center">' + device.screw_size + '</td>';
            rowsStream1 += '<td class="text-center">' + device.quantity +'</td>';
            rowsStream1 += '<td class="text-center">' + device.optionsPs +'</td>';
            rowsStream1 += '</tr>';

        }

        rowsHolder.append(rowsStream);
        rowsHolder1.append(rowsStream1);

        //Refresh the form :
        var device_form_ = $("#deviceForm");
        device_form_.find("input[type='checkbox']").prop("checked",false);
        device_form_.find("select").find("option:first").prop("selected",true);
        device_form_.find("input[type='radio']:nth(1)").prop("checked",true);
        return true;
    };

    // get value elements of special form into table
    var refreshDeviceTable_order = function() {
        var rowsHolder = $(".deviceListRowsHolder");
        var rowsHolder1 = $(".deviceListRowsHolder1");
        rowsHolder.html("");
        rowsHolder1.html("");
        if (deviceArray.length == 0) {
            rowsHolder.html("");
            rowsHolder.parent().addClass('hide');
            return false;
        }else{
            rowsHolder.parent().removeClass('hide');
        }
        if (deviceArray.length == 0) {
            rowsHolder1.html("");
            rowsHolder1.parent().addClass('hide');
            return false;
        }else{
            rowsHolder1.parent().removeClass('hide');
        }

        var rowsStream = "";

        for (var i = 0; i < deviceArray.length; i++) {
            var device = deviceArray[i];

            rowsStream += '<tr data-id='+ device.id +'>';
            rowsStream += '<td class="text-center"><a class="btn-editDevice" data-edit-id="'+ device.id +'" data-id="' + (i) + '" href="#"><i class="glyphicon glyphicon-pencil"></i></a><span class="seperator"> | </span><a data-delete-id="'+ device.id +'" class="btn-delete" data-id="' + (i) + '" href="#"><i class="glyphicon glyphicon-trash"></i></a></td>';
            rowsStream += '<td class="text-center">' + device.optionsPs +'</td>';
            rowsStream += '<td class="text-center">' + device.quantity +'</td>';
            rowsStream += '<td class="text-center">' + device.screw_size + '</td>';
            rowsStream += '<td class="text-center">' + device.screw_type + '</td>';
            rowsStream += '<td class="text-center">' + device.clamping + '</td>';
            rowsStream += '<td class="text-center">' + device.machine_type + '</td>';
            rowsStream += '<td class="text-center">' + device.main_drive + '</td>';
            rowsStream += '<td class="text-center">' + (i+1) +'</td>';
            rowsStream += '</tr>';

        }

        rowsHolder.append(rowsStream);
        rowsHolder1.append(rowsStream);

        //Refresh the form :
        var device_form_ = $("#deviceForm");
        device_form_.find("input[type='checkbox']").prop("checked",false);
        device_form_.find("select").find("option:first").prop("selected",true);
        device_form_.find("input[type='radio']:nth(1)").prop("checked",true);
        return true;
    };

    var deviceArray = new Array();

    var options = "";
    var quantity = "";

    $("#btnAddToList").bind("click",function (e) {
        e.preventDefault();

        var pageType = $('#pageType').val(),
            index_ = $("#id_device").val(),
            page_type_name = "";

        if(pageType == "saleSpecial") {

            page_type_name = "sale";

        } else if(pageType == "orderSpecial") {

            page_type_name = "order";
        }

        if (($("input[name=quantity]").val()=='')||($("#clamping_force").val()=='')||($("#machine_type").val()=='')||($("#screw_type").val()=='')||($("#screw_size").val()=='')) {
            if ($("input[name=quantity]").val()=='') {
                $("input[name=quantity]").css("border-color","crimson");
            }

            if ($("#clamping_force").val()=='') {
                $("#clamping_force").css("border-color","crimson");
            } else {
                $("#clamping_force").css("border-color","#DFE3E7");
            }

            if ($("#machine_type").val()=='') {
                $("#machine_type").css("border-color","crimson");
            } else {
                $("#machine_type").css("border-color","#DFE3E7");
            }

            if ($("#screw_type").val()=='') {
                $("#screw_type").css("border-color","crimson");
            } else {
                $("#screw_type").css("border-color","#DFE3E7");
            }

            if ($("#screw_size").val()=='') {
                $("#screw_size").css("border-color","crimson");
            } else {
                $("#screw_size").css("border-color","#DFE3E7");
            }

            $.bootstrapGrowl("لطفا فیلدها را پر نمایید.", {
                type: 'danger',
                align: 'center',
                width: 'auto',
                allow_dismiss: true
            });

        } else {
            if ($("input[name=quantity]").val()!='') {
                $("input[name=quantity]").css("border-color","#DFE3E7");
            }

            if ($("#clamping_force").val()!='') {
                $("#clamping_force").css("border-color","#DFE3E7");
            }

            if ($("#machine_type").val()!='') {
                $("#machine_type").css("border-color","#DFE3E7");
            }

            if ($("#screw_type").val()!='') {
                $("#screw_type").css("border-color","#DFE3E7");
            }

            if ($("#screw_size").val()!='') {
                $("#screw_size").css("border-color","#DFE3E7");
            }

            var machine = new Object();

            machine.machine_type = $("#machine_type :selected").val();
            machine.main_drive =  $(':input[name="main_drive"]:checked').val();
            machine.clamping = $("#clamping_force :selected").val();
            machine.screw_type = $("#screw_type :selected").val();
            machine.screw_size = $("#screw_size :selected").val();
            machine.pageType = $("#pageType").val();
            machine.quantity = $("#quantity").val();

            machine.options_ = "";
            $("#deviceOptions input:checked").each(function() {
                var _name_ = $(this).val();
                machine.options_ +=  _name_ + ",";
            });
            machine.options_ = machine.options_.slice(0, -1);

            $.ajax({
                url: page_type_name + '.php?action=addProduct',
                type: 'POST',
                data: { groupProductId: machine.machine_type,
                    mainDrive: machine.main_drive,
                    clampingForceId: machine.clamping,
                    screwTypeId: machine.screw_type,
                    screwSizeId: machine.screw_size,
                    quantity: machine.quantity,
                    options: machine.options_,
                    pageType: machine.pageType,
                    customerId: $("#customer_id_holder").attr("data-id")

                },
                success: function (data) {
                    if (data != 0) {
                        var device = new Object();

                        device.machine_type = $("#machine_type :selected").text();
                        device.main_drive =  $(':input[name="main_drive"]:checked').val();
                        device.clamping = $("#clamping_force :selected").text();
                        device.screw_type = $("#screw_type :selected").text();
                        device.screw_size = $("#screw_size :selected").text();
                        device.quantity = $("#quantity").val();
                        device.id = data;

                        device.optionsPs = "";
                        device.optionsId = "";
                        $("#deviceOptions input:checked").each(function() {
                            var name = $(this).attr("data-value");
                            var id = $(this).val();
                            device.optionsPs += name + ",";
                            device.optionsId += id + ",";
                        });
                        device.optionsPs = device.optionsPs.slice(0, -1);
                        device.optionsId = device.optionsId.slice(0, -1);

                        deviceArray.push(device);
                        device = null;

                        if(page_type_name == "sale") {
                            refreshDeviceTable();
                        } else if(page_type_name == "order") {
                            refreshDeviceTable_order();
                        }
                        // refresh inputs
                        refreshInputs();

                        $.bootstrapGrowl("با موفقیت اضافه شد.", {
                            type: 'success',
                            align: 'center',
                            width: 'auto',
                            allow_dismiss: true
                        });
                    } else {
                        $.bootstrapGrowl("مشکل در ارسال اطلاعات ..", {
                            type: 'danger',
                            align: 'center',
                            width: 'auto',
                            allow_dismiss: true
                        });
                    }
                }
            });

        }

    });

    $body.on("click",".btn-delete",function() {
        $("#id_device").val($(this).attr("data-id"));
        $("#productIdDelete").val($(this).attr("data-delete-id"))
        $("#mdlDeleteConfirm").modal("show");
    });

    $("#btnAcceptDelete").bind("click",function(){
        var pageType = $('#pageType').val(),
            index_ = $("#id_device").val(),
            page_type_name = "";

        if(pageType == "saleSpecial") {

            page_type_name = "sale";

        } else if(pageType == "orderSpecial") {

            page_type_name = "order";
        }

        $.ajax({
            url: page_type_name + '.php?action=deleteProduct',
            type: 'POST',
            data: {
                productId: $("#productIdDelete").val()
            },
            success: function (data) {
                if (data == 1) {
                    $.bootstrapGrowl("با موفقیت حذف شد.", {
                        type: 'success',
                        align: 'center',
                        width: 'auto',
                        allow_dismiss: true
                    });
                } else {
                    $.bootstrapGrowl("مشکل در حذف ماشین ...", {
                        type: 'danger',
                        align: 'center',
                        width: 'auto',
                        allow_dismiss: true
                    });
                }
            }
        });

        var tempArray = new Array();
        for (var i = 0; i < deviceArray.length; i++) {
            if ( i != parseInt(index_)) {
                tempArray.push(deviceArray[i])
            }
        }

        deviceArray = tempArray;

        // when page loaded get type of page and refresh table of form special with current page type
        if(page_type_name == "order") {
            refreshDeviceTable_order();
        } else if(page_type_name == "sale") {
            refreshDeviceTable();
        }
        refreshInputs();

        $("#mdlDeleteConfirm").modal("hide");
    });

    var $customModal = $("#mdlEditDevice");

    $body.on("click",".btn-editDevice",function(e) {
        e.preventDefault();

        $("#deviceOptions-e input[type=checkbox]").each(function() {
            $(this).prop("checked",false);
        });

        var device = deviceArray[$(this).attr("data-id")];
        console.log(device);
        $('input[name="main_drive_e"][value="'+device.main_drive+'"]').prop("checked",true);

        $("#machine_type_e option").each(function() {
            if ($(this).text() == device.machine_type) {
                $(this).prop("selected",true);
            }
        });

        $("#clamping_force_e option").each(function() {
            if ($(this).text() == device.clamping) {
                $(this).prop("selected",true);
            }
        });

        $("#screw_type_e option").each(function() {
            if ($(this).text() == device.screw_type) {
                $(this).prop("selected",true);
            }
        });

        var  mainDrive = $("input[name=main_drive_e]:checked").val();
        var  clampingForceId = $("#clamping_force_e option:selected").val();
        var  screwSize = $("#screw_size_e");
        var  rowsStream = '';
        screwSize.find('option').remove();

        $.ajax({
            url: 'sale.php?action=getScrewSize',
            type: 'post',
            data: {
                mainDrive: mainDrive,
                clampingForceId: clampingForceId
            },
            dataType: 'json',
            success: function (data) {

                rowsStream += '<option value = "' + data["screw_size_id"] + ' ' + data["name_a"] + '" ' + ((parseInt(data["name_a"]) == parseInt(device.screw_size))? 'selected' :null) + '>' + data["name_a"] + '</option>';
                rowsStream += '<option value = "' + data["screw_size_id"] + ' ' + data["name_b"] + '" ' + ((parseInt(data["name_b"]) == parseInt(device.screw_size))? 'selected' :null) + '>' + data["name_b"] + '</option>';
                rowsStream += '<option value = "' + data["screw_size_id"] + ' ' + data["name_c"] + '" ' + ((parseInt(data["name_c"]) == parseInt(device.screw_size))? 'selected' :null) + '>' + data["name_c"] + '</option>';

                screwSize.append(rowsStream);

            }

        });

        $("#quantity_e").val(device.quantity);

        var options_ = device.optionsId.split(",");
        for (var i = 0; i < (options_.length); i++) {
            $('#deviceOptions-e input[value="'+ options_[i] +'"]').prop("checked",true);
        }

        $("#id_device-e").val($(this).attr("data-id"));
        $("#id_product-e").val($(this).attr("data-edit-id"));

        $customModal.modal("show");
    });

    $body.on("click",".btn-reserveDevice",function() {
        $("#machineReserveId").val($(this).attr("data-reserve-id"));

        $("#reserveMachineModal").modal("show");
    });

    $("#btnAcceptReserve").bind("click",function(){
        $.ajax({
            url: 'sale.php?action=reserveProduct',
            type: 'POST',
            data: {
                productId: $("#machineReserveId").val()
            },
            success: function (data) {
                if (data == 1) {
                    $.bootstrapGrowl("با موفقیت رزرو شد.", {
                        type: 'success',
                        align: 'center',
                        width: 'auto',
                        allow_dismiss: true
                    });
                } else {
                    $.bootstrapGrowl("مشکل در حذف ماشین ...", {
                        type: 'danger',
                        align: 'center',
                        width: 'auto',
                        allow_dismiss: true
                    });
                }
            }
        });
        $("#reserveMachineModal").modal("hide");
    });

    $("#btnEditDeviceAccept").bind("click",function() {
        var pageType = $('#pageType').val(),
            page_type_name = "",
            optionsVal = "";

        if(pageType == "saleSpecial") {

            page_type_name = "sale";

        } else if(pageType == "orderSpecial") {

            page_type_name = "order";
        }

        var productID = $("#id_product-e").val();

        $("#deviceOptions-e input:checked").each(function() {
            var id = $(this).val();
            optionsVal +=  id + ",";
        });
        optionsVal = optionsVal.slice(0, -1);

        var options = optionsVal;

        $.ajax({
            url: page_type_name + '.php?action=editProduct',
            type: 'POST',
            data: {
                productId: productID,
                groupProductId: $("#machine_type_e :selected").val(),
                mainDrive: $('input[name="main_drive_e"]:checked').val(),
                clampingForceId: $("#clamping_force_e :selected").val(),
                screwTypeId: $("#screw_type_e :selected").val(),
                screwSizeId: $("#screw_size_e :selected").val(),
                quantity: $("#quantity_e").val(),
                options: options
            },
            success: function (data) {
                if (data == 1) {
                    $.bootstrapGrowl("با موفقیت اصلاح شد .", {
                        type: 'success',
                        align: 'center',
                        width: 'auto',
                        allow_dismiss: true
                    });
                } else {
                    $.bootstrapGrowl("مشکل در اصلاح ماشین ...", {
                        type: 'danger',
                        align: 'center',
                        width: 'auto',
                        allow_dismiss: true
                    });
                }
            }
        });

        var device = new Object();
        device.machine_type = $("#machine_type_e :selected").text();
        device.main_drive =  $(':input[name="main_drive_e"]:checked').val();
        device.clamping = $("#clamping_force_e :selected").text();
        device.screw_type = $("#screw_type_e :selected").text();
        device.screw_size = $("#screw_size_e :selected").text();
        device.quantity = $("#quantity_e").val();

        device.optionsPs = "";
        device.optionsId = "";
        $("#deviceOptions-e input:checked").each(function() {
            var _name_ = $(this).attr("data-value");
            var _id_ = $(this).attr("value");
            device.optionsPs +=  _name_ + ",";
            device.optionsId +=  _id_ + ",";
        });
        device.optionsPs = device.optionsPs.slice(0, -1);
        //device.optionsId = device.optionsId.slice(0, -1);

        deviceArray[$("#id_device-e").val()] = device;

        if (page_type_name == 'sale') {

            $('.deviceLisTable tr[data-id="'+productID+'"] td:eq(5)').text(device.optionsPs);
            $('.deviceLisTable tr[data-id="'+productID+'"] td:eq(4)').text(device.quantity);
            $('.deviceLisTable tr[data-id="'+productID+'"] td:eq(3)').text(device.clamping);
            $('.deviceLisTable tr[data-id="'+productID+'"] td:eq(2)').text(device.screw_type);
            $('.deviceLisTable tr[data-id="'+productID+'"] td:eq(1)').text(device.main_drive+device.clamping+device.machine_type);

        } else if (page_type_name == 'order') {

            $('.deviceLisTable tr[data-id="'+productID+'"] td:eq(7)').text(device.main_drive);
            $('.deviceLisTable tr[data-id="'+productID+'"] td:eq(6)').text(device.machine_type);
            $('.deviceLisTable tr[data-id="'+productID+'"] td:eq(5)').text(device.clamping);
            $('.deviceLisTable tr[data-id="'+productID+'"] td:eq(4)').text(device.screw_type);
            $('.deviceLisTable tr[data-id="'+productID+'"] td:eq(3)').text(device.screw_size);
            $('.deviceLisTable tr[data-id="'+productID+'"] td:eq(2)').text(device.quantity);
            $('.deviceLisTable tr[data-id="'+productID+'"] td:eq(1)').text(device.optionsPs);

        }

        /*if(page_type_name == "sale") {
         refreshDeviceTable();
         } else {
         refreshDeviceTable_order();
         }*/
        $("#mdlEditDevice").modal("hide");
    });


    $("#saleType").bind("change",function() {
        if ($(this).val() == "cash") {
            $("#ovlSaleType").text("نقدی");
        }else{
            $("#ovlSaleType").text("اعتباری");
        }
    });

    $('[data-mask="percent"]').mask('00%', {reverse: true});


    /*
     Begin: Accounting JS
     */

    var field_price = $("#field_price");
    field_price.bind('keydown keyup keypress focus blur paste change', function() {
        var finalLabel = accounting.formatNumber($(this).val()) + " ریال";
        var priceIndic =$("#priceIndicator");
        priceIndic.text(finalLabel);

        priceIndic.append("<span style='color:#f30' id='6_vat_indic'> + " +  accounting.formatNumber((($("#field_6_vat").val().slice(0,-1) / 100) * $(this).val())) + " ریال" + " مالیات بر ارزش افزوده </span>");

        $("#remAmount").text(finalLabel);
    });

    var field_downPay = $("#field_down_payment");
    field_downPay.bind('keydown keyup keypress focus blur paste change', function() {
        $("#downPaymentIndicator").text(accounting.formatNumber($(this).val()) + " ریال")
        $("#downpayment").text(accounting.formatNumber($(this).val()) + " ریال")
    });

    $("#field_installment").bind('keydown keyup keypress focus blur paste change', function() {
        $("#numberInstallment").text($(this).val());
    });

    var chqAmount = $("#chqAmount");
    chqAmount.bind('keydown keyup keypress focus blur paste change', function() {
        $("#chqAmountIndic").text(accounting.formatNumber($(this).val()) + " ریال")
    });

    var chqAmount_e = $("#chqAmount-e");
    chqAmount_e.bind('keydown keyup keypress focus blur paste change', function() {
        $("#chqAmountIndic-e").text(accounting.formatNumber($(this).val()) + " ریال")
    });

    $("#field_price,#field_down_payment,#field_installment,#field_6_vat").bind("keyup",function() {
        calculateTotal();
        calculateInstallment();
    });

    var calculateTotal = function() {
        var price_ = $("#field_price").val();
        var calculatePrice = accounting.unformat($("#calculated-price").val());

        if(isNaN(price_) ) return false;

        // discount
        $("#discount").text(accounting.formatNumber(calculatePrice - price_));
        $("#fields_discount").val(accounting.formatNumber(calculatePrice - price_));
        $("#ovlDiscount").text(accounting.formatNumber(calculatePrice - price_) + " ریال");

        // tax
        var prizeIndic = $("#priceIndicator");
        prizeIndic.text(accounting.formatNumber(price_) + " ریال");
        prizeIndic.append("<span style='color:#f30' id='6_vat_indic'> + " +  accounting.formatNumber((($("#field_6_vat").val().slice(0,-1) / 100) * price_)) + " ریال" + " مالیات بر ارزش افزوده </span>");
        $("#vat").text(accounting.formatNumber((($("#field_6_vat").val().slice(0,-1) / 100) * price_)) + " ریال");
        $("#ovlVat").text(accounting.formatNumber((($("#field_6_vat").val().slice(0,-1) / 100) * price_)) + " ریال");

        $("#salePrice").text(accounting.formatNumber(parseInt(price_)) + 'ریال');
        $("#totalAmount").text(accounting.formatNumber(parseInt(price_) + (($("#field_6_vat").val().slice(0,-1) / 100) * parseInt(price_))));
        $("#ovlTotal").text(accounting.formatNumber(parseInt(price_) + (($("#field_6_vat").val().slice(0,-1) / 100) * parseInt(price_))) + "ریال");
        $("#ovlPrice").text(accounting.formatNumber(parseInt(price_))  + " ریال");
    };


    var calculateInstallment = function() {
        var price_ = parseInt($("#field_price").val());

        if (isNaN(price_)) {
            $("#saleIndicator_").text("۰ ریال");
            return false;
        }

        var downPay = parseInt($("#field_down_payment").val());
        var discount = accounting.unformat($("#fields_discount").val());
        var vat = parseInt((($("#field_6_vat").val().slice(0,-1) / 100) * price_));

        if (isNaN(downPay)){
            $("#saleIndicator_").text(accounting.formatNumber(price_) + " ریال");
            return false;
        }

        $("#ovlDownPay").text(accounting.formatNumber(downPay) + " ریال");

        var dividable_amount = (price_ - downPay);

        if (dividable_amount < 0 ) {
            $("#saleIndicator_").text("0 ریال");
        }else{
            $("#saleIndicator_").text(accounting.formatNumber(dividable_amount) + " ریال");
            $("#remAmount").text(accounting.formatNumber(dividable_amount) + " ریال");
        }

        var installment_ = $("#field_installment").val(),
            installmentOve;

        if (isNaN(installment_)) {
            installmentOve = 0;
        } else {
            installmentOve = installment_;
        }

        if (dividable_amount != 0 && installment_ != 0) {
            var installment_amount = dividable_amount / installment_;
            $("#ovlInstallment").text(installmentOve);
            $("#installAmountIndicator").text(accounting.formatNumber(installment_amount) + " ریال");
            $("#ovlMiddle").text(accounting.formatNumber(installment_amount) + " ریال");
        }
    };

    // END: Accounting JS

    /**
     * model in form special
     * @type {*|jQuery|HTMLElement}
     */
    var model = $('#machine_type');
    model.change(function(){
        var option = $("#deviceOptions input");
        option.each(function() {
            if ($(this).attr("data-model") != model.val()) {
                $(this).prop("checked",false);
            }
            if ($(this).attr("data-model") == model.val()) {
                $(this).prop("checked",true);
            }
        });
    });
    var model_onModal = $('#machine_type_e');
    model_onModal.change(function(){
        var option = $("#deviceOptions-e input");
        option.each(function() {
            if ($(this).attr("data-model") != model_onModal.val()) {
                $(this).prop("checked",false);
            }
            if ($(this).attr("data-model") == model_onModal.val()) {
                $(this).prop("checked",true);
            }
        });
    });

    $('#add_Charecter_user').bind("click",function(){
        var customerId = $('#customer_id_holder').attr("data-id");

        $.ajax({
            url: 'sale.php?action=getCustomerData&id=' + customerId,
            dataType: 'json',
            success: function (data) {
                var mobile = data['phone'];
                var address = data['address'];
                $("#mobile_td").text(mobile);
                $("#address_td").text(address);
                $(':input[name=mobile_check]').attr('data-mobile',mobile);
                $(':input[name=address_check]').attr('data-address',address);
            }
        });
        $('#addModalCustomerCharecter').modal('show');
    });

    $('#btnAddCustomeChare').bind("click",function(){
        var check_address = $('#address_check'),
            check_mobile = $('#mobile_check');
        if ($(check_mobile).is(':checked') && $(check_address).is(':checked')) {

            $('#ovlCustomerAddress').text($('#address_check').attr('data-address'));
            $('#ovlCustomerMobile').text($('#mobile_check').attr('data-mobile'));
            $('#personalInfo div.mobile').removeClass('hide');
            $('#personalInfo div.address').removeClass('hide');
        }
        if ($(check_mobile).is(':checked') && !$(check_address).is(':checked')) {

            $('#ovlCustomerMobile').text($('#mobile_check').attr('data-mobile'));
            $('#personalInfo div.mobile').removeClass('hide');
            $('#personalInfo div.address').addClass('hide');

        }
        if (!$(check_mobile).is(':checked') && $(check_address).is(':checked')) {

            $('#ovlCustomerAddress').text($('#address_check').attr('data-address'));
            $('#personalInfo div.address').removeClass('hide');
            $('#personalInfo div.mobile').addClass('hide');

        }
        if (!$(check_mobile).is(':checked') && !$(check_address).is(':checked')) {

            $('#personalInfo div.address').addClass('hide');
            $('#personalInfo div.mobile').addClass('hide');

        }
        $('#addModalCustomerCharecter').modal('hide');

    });


    // cheque JS
    $("#btnAddChequeInfo").on("click",function() {
        var  randNum = $("input[name=checkRandInvoiceNum]");

        if (randNum.val() == '') {

            // remaining price
            var dividable = parseInt(accounting.unformat($("#field_price").val())) - parseInt(accounting.unformat($("#field_down_payment").val()));
            var target_modal_ = $("#mdlCheque");
            var price_ = parseInt($("#field_price").val());

            $("#remAmount").text(accounting.formatNumber(dividable));
            $("#taxAmount").text(accounting.formatNumber(($("#field_6_vat").val().slice(0,-1) / 100) * price_));

            var sale_price = parseInt($("#field_price").val());

            if (isNaN(sale_price)) {
                $("#btnAddChqInfo").attr("disabled",true);
                $("#btnPayTaxWithCheque").attr("disabled",true);
            }else{
                $("#btnAddChqInfo").attr("disabled",false);
                $("#btnPayTaxWithCheque").attr("disabled",false);
            }

            target_modal_.modal("show");

            var chqCount = parseInt($("#field_installment").val());

            if(isNaN(chqCount) ) {
                chqCount = 1;
            }

            var items = "";

            for (var i = 1 ; i <= chqCount ; i++) {
                items += "<tr>" +
                "<td><input name='check_amount' class='form-control col-sm-10 centerize chq_amount' type='text' style='width:150px;height:25px'/></td>" +
                "<td><input name='bank_check' class='form-control col-sm-10 centerize' type='text' style='width:150px;height:25px'/></td>" +
                "<td><input name='shobe_check' class='form-control col-sm-10 centerize' type='text' style='width:100px'/></td>"+
                "<td><input name='date_check' class='form-control col-sm-10 centerize chq_date_holder' type='text' style='width:100px'/></td>"+
                "<td><input name='check_number' class='form-control col-sm-10 centerize' type='text' style='width:100px'/></td>"+
                "<td><a class='btn-clearChqInfo' data-id='0' href='#'>" +
                "<i class='glyphicon glyphicon-ban-circle' title='پاک کردن'></i></a>" +
                "</tr>";
            }

            $("#chq_items").html(items);
            $("#chq_items input").css("height","25px");

            var $datePicker = $("input[name=date_check]");
            $datePicker.persianDatepicker();

            $(".chq_amount").bind('keyup paste change', function() {
                $(this).val(accounting.formatNumber($(this).val()));
                var remHolder = $("#realDividableAmount");

                var totalMineses = 0;

                $(".chq_amount").each(function() {
                    totalMineses = totalMineses + parseInt(accounting.unformat($(this).val()));
                });

                totalMineses =  totalMineses + parseInt($("#field_down_payment").val());
                var dividable = 0;
                dividable =  parseInt($("#field_price").val()) - totalMineses;

                if (dividable < 0 )
                {
                    remHolder.val(0);
                    $(this).val(accounting.formatNumber($(this).val()));
                }else{
                    remHolder.val(dividable);
                }

                $("#remAmount").text(accounting.formatNumber(remHolder.val()) + " ریال");
            });

        } else {

            $.ajax({
                url: 'sale.php',
                type: 'POST',
                data: {
                    action: 'getChecks',
                    rand: randNum.val()
                },
                dataType: 'json',
                success: function (data) {

                    var arr =[];
                    for( var i in data ) {
                        if (data.hasOwnProperty(i)){
                            arr.push(data[i]);
                        }
                    }

                    var itemss = "";
                    $.each(arr,function(key,value){

                        itemss += "<tr>" +
                                "<td><input name='check_amount' class='form-control col-sm-10 centerize chq_amount' type='text' style='width:150px;height:25px' value='" + value['amount'] + "' /></td>" +
                                "<td><input name='bank_check' class='form-control col-sm-10 centerize' type='text' style='width:150px;height:25px' value='" + value['bank'] + "'/></td>" +
                                "<td><input name='shobe_check' class='form-control col-sm-10 centerize' type='text' style='width:100px' value='" + value['shobe'] + "'/>" + value['shobe'] + "</td>" +
                                "<td><input name='date_check' class='form-control col-sm-10 centerize chq_date_holder' type='text' style='width:100px' value='" + value['date'] + "' /></td>" +
                                "<td><input name='check_number' class='form-control col-sm-10 centerize' type='text' style='width:100px' value='" + value['number'] + "' /></td>" +
                                "<td><a class='btn-clearChqInfo' data-id='0' href='#'>" +
                                "<i class='glyphicon glyphicon-ban-circle' title='پاک کردن'></i></a></td>" +
                                "</tr>";

                    })

                    $("#chq_items").html(itemss);
                    $("#chq_items input").css("height","25px");

                    $("#mdlCheque").modal("show");

                }
            });

        }

    });


    $('#btnPayTaxWithCheque').bind("click",function(){
        var items = "";

        items += "<tr>" +
            "<td><input name='check_amount' data-tax-amount='amount' class='form-control col-sm-10 centerize chq_amount' type='text' style='width:150px;height:25px'/></td>" +
            "<td><input name='bank_check' class='form-control col-sm-10 centerize' type='text' style='width:150px;height:25px'/></td>" +
            "<td><input name='shobe_check' class='form-control col-sm-10 centerize' type='text' style='width:100px'/></td>"+
            "<td><input name='date_check' data-tax-date='date' class='form-control col-sm-10 centerize chq_date_holder' type='text' style='width:100px'/></td>"+
            "<td><input name='check_number' class='form-control col-sm-10 centerize' type='text' style='width:100px'/></td>"+
            "<td><a class='btn-clearChqInfo' data-id='0' href='#'><i class='glyphicon glyphicon-ban-circle' title='پاک کردن'></i></a>" +"<a class='btn-deleteChqInfo' data-id='1' href='#'><i class='glyphicon glyphicon-trash' title='حذف کردن'></i></a>" +
            "</tr>";

        $("#chq_items").append(items);
        $("#chq_items input").css("height","25px");

        $(".chq_date_holder").persianDatepicker();
        $("input[data-tax-date='date']").persianDatepicker();
        var date = $("input[name=date_check]");
        date.persianDatepicker();

    });

    $(".chq_date_holder").persianDatepicker();
    $("input[data-tax-date='date']").persianDatepicker();
    var date = $("input[name=date_check]");
    date.persianDatepicker();

    $body.on("click",".btn-deleteChqInfo",function(e) {
        e.preventDefault();
        $(this).parents("tr").remove();
    });

    $("#btnAddChqInfo").bind("click",function(e) {
        e.preventDefault();

        var rand = $("input[name=checkRandInvoiceNum]");

        var listCheck_amount = [];
        var listCheck_bank = [];
        var listCheck_shobe = [];
        var listCheck_date = [];
        var listCheck_number = [];
        $(':input[name=check_amount]').each(function(){
            listCheck_amount.push(accounting.unformat($(this).val()));
        });
        $(':input[name=bank_check]').each(function(){
            listCheck_bank.push($(this).val());
        });
        $(':input[name=shobe_check]').each(function(){
            listCheck_shobe.push($(this).val());
        });
        $(':input[name=date_check]').each(function(){
            listCheck_date.push($(this).val());
        });
        $(':input[name=check_number]').each(function(){
            listCheck_number.push($(this).val());
        });

        $.ajax({
            url: 'sale.php?action=saveCheckInfo',
            type: 'POST',
            data: {
                rand: rand.val(),
                listCheck_amount: listCheck_amount,
                listCheck_bank: listCheck_bank,
                listCheck_shobe: listCheck_shobe,
                listCheck_date: listCheck_date,
                listCheck_number: listCheck_number
            },
            success: function (data) {
                if (data != 0) {
                    rand.val(data);
                    $.bootstrapGrowl("با موفقیت ثبت شد.", {
                        type: 'success',
                        align: 'center',
                        width: 'auto',
                        allow_dismiss: true
                    });
                } else {
                    $.bootstrapGrowl("مشکل در ثبت اطلاعات ...", {
                        type: 'danger',
                        align: 'center',
                        width: 'auto',
                        allow_dismiss: true
                    });
                }
            }
        });

        $("#mdlCheque").modal("hide");
    });

    $body.on("click",".btn-clearChqInfo",function(e) {
        e.preventDefault();
        $(this).parents("tr").find("input").val("");
        $(this).parents("tr").find("select option:nth-child(1)").prop("selected",true);
    });

    // End cheque

    // script to ready document for print
    $('#btnReadyToPrint').bind('click',function(e){
        e.preventDefault();
        $('.clean').removeClass('hide');
        $('#resetDoc').removeClass('hide');
    });

    // script to reset form for hide element ready to print
    $('#resetDoc').bind('click',function(e){
        e.preventDefault();
        $('.clean').addClass('hide');
//        $('div[data-role="print"]').removeClass("hide");
        $(this).addClass('hide');
    });

    // click to clean button to remove printable label
    $('.clean').bind('click',function(){
        $(this).parents('div[data-role="print"]').addClass("hide");
    });

    var deviceArray = new Array();
    var chequeArray = new Array();
    var total_chqAmount = 0;

    var rowCount = $('.deviceListRowsHolder');
    if(deviceArray.length == 0){
        rowCount.parents('.table').addClass('hide');
    }else{
        rowCount.parents('.table').removeClass('hide');
    }

    $("#chqDueDate").persianDatepicker();
    $("#chqDueDate-e").persianDatepicker();
    $("#field_chq_date").persianDatepicker();
    var $datePicker = $("input[name=date_check]");
    $datePicker.persianDatepicker();


    $("#btnSend2Incomplete").bind("click",function(e){
        e.preventDefault();

        $("input[name=btnName]").val("btnSend2Incomplete");

        $("input[name=field_saleTable_userAddress]").val($("#ovlCustomerAddress").text());
        $("input[name=field_saleTable_userPhone]").val($("#ovlCustomerMobile").text());
        $("#saleTable_form").submit();

    });

    $('#btnSend2Archive_order').bind('click',function(e){
        $.bootstrapGrowl("سفارش مورد نظر با موفقیت به بایگانی منتقل شد" , {
            type: 'success',
            align: 'center',
            width: 'auto',
            allow_dismiss: false
        });
    });

    $('#btnSend2Incomplete_order').bind('click',function(e){
        $.bootstrapGrowl("سفارش مورد نظر با موفقیت به سفارشات ناتمام منتقل شد", {
            type: 'success',
            align: 'center',
            width: 'auto',
            allow_dismiss: false
        });
    });


    $("#btnSend2Archive").bind("click",function(e){
        e.preventDefault();

        $("input[name=btnName]").val("btnSend2Archive");

        $("input[name=field_saleTable_userAddress]").val($("#ovlCustomerAddress").text());
        $("input[name=field_saleTable_userPhone]").val($("#ovlCustomerMobile").text());
        $("#saleTable_form").submit();
    });

    $("#addSubmit").bind("click",function(e){
        e.preventDefault();
        var lengthChecked = $("#autoOrderTable input:checked").length;

        if (lengthChecked != 0) {
            $("#autoSuggestForm").submit();
        } else {
            $.bootstrapGrowl("برای ثبت سفارش حداقل یک دستگاه را انتخاب نمایید.", {
                type: 'danger',
                align: 'center',
                width: 'auto',
                allow_dismiss: true
            });
        }
    });

    $("#removeSubmit").bind("click",function(e){
        e.preventDefault();
        var lengthChecked = $("#autoOrderTable input:checked").length;

        if (lengthChecked != 0) {
            var totalVal = [];

            $("#autoOrderTable input:checked").each(function(){
                totalVal.push($(this).val());
            });
            $("#removeSuggest").val(totalVal);

            $("#autoSuggestForm").submit();
        } else {
            $.bootstrapGrowl("موردی برای حذف وجود ندارد!", {
                type: 'danger',
                align: 'center',
                width: 'auto',
                allow_dismiss: true
            });
        }
    });

    $(".archiveSaleBtn").bind("click",function(){
        $("#saleIdArchive").val($(this).attr("data-id"));
        $("#saleArchiveModal").modal("show");
    });

    $(".cancelSaleBtn").bind("click",function(){
        $("#cancelSaleId").val($(this).attr("data-id"));
        $("#cancelIncompleteSale").modal("show");
    });

// TABLESORTER
    $.extend($.tablesorter.themes.bootstrap, {
        // these classes are added to the table. To see other table classes available,
        // look here: http://twitter.github.com/bootstrap/base-css.html#tables
        table      : 'table',
        caption    : 'caption',
        header     : 'bootstrap-header', // give the header a gradient background
        footerRow  : '',
        footerCells: '',
        icons      : '', // add "icon-white" to make them white; this icon class is added to the <i> in the header
        sortNone   : 'fa fa-sort',
        sortAsc    : 'fa fa-sort-asc',     // includes classes for Bootstrap v2 & v3
        sortDesc   : 'fa fa-sort-desc', // includes classes for Bootstrap v2 & v3
        active     : '', // applied when column is sorted
        hover      : '', // use custom css here - bootstrap class may not override it
        filterRow  : '', // filter row class
        even       : '', // odd row zebra striping
        odd        : ''  // even row zebra striping
    });

    // **********************************
    //  Description of ALL pager options
    // **********************************
    var pagerOptions = {

        // target the pager markup - see the HTML block below
        container: $(".ts-pager"),

        // use this url format "http:/mydatabase.com?page={page}&size={size}&{sortList:col}"
        ajaxUrl: null,

        // modify the url after all processing has been applied
        customAjaxUrl: function(table, url) { return url; },

        // process ajax so that the data object is returned along with the total number of rows
        // example: { "data" : [{ "ID": 1, "Name": "Foo", "Last": "Bar" }], "total_rows" : 100 }
        ajaxProcessing: function(ajax){
            if (ajax && ajax.hasOwnProperty('data')) {
                // return [ "data", "total_rows" ];
                return [ ajax.total_rows, ajax.data ];
            }
        },

        // output string - default is '{page}/{totalPages}'
        // possible variables: {page}, {totalPages}, {filteredPages}, {startRow}, {endRow}, {filteredRows} and {totalRows}
        output: '{startRow} to {endRow} ({totalRows})',

        // apply disabled classname to the pager arrows when the rows at either extreme is visible - default is true
        updateArrows: true,

        // starting page of the pager (zero based index)
        page: 0,

        // Number of visible rows - default is 10
        size: 10,

        // Save pager page & size if the storage script is loaded (requires $.tablesorter.storage in jquery.tablesorter.widgets.js)
        savePages : true,

        // if true, the table will remain the same height no matter how many records are displayed. The space is made up by an empty
        // table row set to a height to compensate; default is false
        fixedHeight: true,

        // remove rows from the table to speed up the sort of large tables.
        // setting this to false, only hides the non-visible rows; needed if you plan to add/remove rows with the pager enabled.
        removeRows: false,

        // css class names of pager arrows
        cssNext: '.next', // next page arrow
        cssPrev: '.prev', // previous page arrow
        cssFirst: '.first', // go to first page arrow
        cssLast: '.last', // go to last page arrow
        cssGoto: '.gotoPage', // select dropdown to allow choosing a page

        cssPageDisplay: '.pagedisplay', // location of where the "output" is displayed
        cssPageSize: '.pagesize', // page size selector - select dropdown that sets the "size" option

        // class added to arrows when at the extremes (i.e. prev/first arrows are "disabled" when on the first page)
        cssDisabled: 'disabled', // Note there is no period "." in front of this class name
        cssErrorRow: 'tablesorter-errorRow' // ajax error information row
    };

    // Initialize tablesorter
    // ***********************
    $('.tablesorter').tablesorter({
        // this will apply the bootstrap theme if "uitheme" widget is included
        // the widgetOptions.uitheme is no longer required to be set
        theme : "bootstrap",

        widthFixed: true,

        headerTemplate : '{content} {icon}', // new in v2.7. Needed to add the bootstrap icon!

        // widget code contained in the jquery.tablesorter.widgets.js file
        // use the zebra stripe widget if you plan on hiding any rows (filter widget)
        widgets : [ "uitheme", "filter" ],

        widgetOptions : {
            // reset filters button
            filter_reset : ".reset"

            // set the uitheme widget to use the bootstrap theme class names
            // this is no longer required, if theme is set
            // ,uitheme : "bootstrap"

        }
    })
        // initialize the pager plugin
        // ****************************
        .tablesorterPager(pagerOptions);
    // END TABLESORTER

    $("#terminatedBtn").bind("click",function(){
        if ($("textarea[name=cancelNote]").text() == '') {
            $.bootstrapGrowl("دلیل فسخ قرارداد را درج نمایید!", {
                type: 'danger',
                align: 'center',
                width: 'auto',
                allow_dismiss: true
            });
        }
    });

    $("#btnAddMachineToBorchList").bind("click",function(e){
        e.preventDefault();

        if (($("#clamping_force").val()=='')||($("#machine_type").val()=='')||($("#screw_type").val()=='')||($("#screw_size").val()=='')||($("#deliveryDateBorch").val()=='')||($("#clampingType").val()=='')) {

            if ($("#clamping_force").val()=='') {
                $("#clamping_force").css("border-color","crimson");
            } else {
                $("#clamping_force").css("border-color","#DFE3E7");
            }

            if ($("#machine_type").val()=='') {
                $("#machine_type").css("border-color","crimson");
            } else {
                $("#machine_type").css("border-color","#DFE3E7");
            }

            if ($("#screw_type").val()=='') {
                $("#screw_type").css("border-color","crimson");
            } else {
                $("#screw_type").css("border-color","#DFE3E7");
            }

            if ($("#screw_size").val()=='') {
                $("#screw_size").css("border-color","crimson");
            } else {
                $("#screw_size").css("border-color","#DFE3E7");
            }

            if ($("#deliveryDateBorch").val()=='') {
                $("#deliveryDateBorch").parent().css("border","1px solid crimson");
            } else {
                $("#deliveryDateBorch").parent().css("border","1px solid #DFE3E7");
            }

            if ($("#clampingType").val()=='') {
                $("#clampingType").css("border-color","crimson");
            } else {
                $("#clampingType").css("border-color","#DFE3E7");
            }

            if ($("input[name=quantity]").val()=='') {
                $("input[name=quantity]").css("border-color","crimson");
            } else {
                $("input[name=quantity]").css("border-color","#DFE3E7");
            }

            $.bootstrapGrowl("Please Fill The Fields !", {
                type: 'danger',
                align: 'center',
                width: 'auto',
                allow_dismiss: true
            });

        } else {

            if ($("#clamping_force").val()!='') {
                $("#clamping_force").css("border-color","#DFE3E7");
            }

            if ($("#machine_type").val()!='') {
                $("#machine_type").css("border-color","#DFE3E7");
            }

            if ($("#screw_type").val()!='') {
                $("#screw_type").css("border-color","#DFE3E7");
            }

            if ($("#screw_size").val()!='') {
                $("#screw_size").css("border-color","#DFE3E7");
            }

            if ($("input[name=quantity]").val()!='') {
                $("input[name=quantity]").css("border-color","#DFE3E7");
            }

            $("#addMachineBorchForm").submit();
        }
    });

    $("#editBtnBorch").bind("click",function(e){
        e.preventDefault();

        if (($("#clamping_force_e").val()=='')||($("#machine_type_e").val()=='')||($("#screw_type_e").val()=='')||($("#screw_size_e").val()=='')||($("#deliveryDateBorch_e").val()=='')||($("#clampingType_e").val()=='')) {

            if ($("#clamping_force_e").val()=='') {
                $("#clamping_force_e").css("border-color","crimson");
            } else {
                $("#clamping_force_e").css("border-color","#DFE3E7");
            }

            if ($("#machine_type_e").val()=='') {
                $("#machine_type_e").css("border-color","crimson");
            } else {
                $("#machine_type_e").css("border-color","#DFE3E7");
            }

            if ($("#screw_type_e").val()=='') {
                $("#screw_type_e").css("border-color","crimson");
            } else {
                $("#screw_type_e").css("border-color","#DFE3E7");
            }

            if ($("#screw_size_e").val()=='') {
                $("#screw_size_e").css("border-color","crimson");
            } else {
                $("#screw_size_e").css("border-color","#DFE3E7");
            }

            if ($("#deliveryDateBorch_e").val()=='') {
                $("#deliveryDateBorch_e").parent().css("border","1px solid crimson");
            } else {
                $("#deliveryDateBorch_e").parent().css("border","1px solid #DFE3E7");
            }

            if ($("#clampingType_e").val()=='') {
                $("#clampingType_e").css("border-color","crimson");
            } else {
                $("#clampingType_e").css("border-color","#DFE3E7");
            }

            if ($("input[name=quantity_e]").val()=='') {
                $("input[name=quantity_e]").css("border-color","crimson");
            } else {
                $("input[name=quantity_e]").css("border-color","#DFE3E7");
            }

            $.bootstrapGrowl("Please Fill The Fields !", {
                type: 'danger',
                align: 'center',
                width: 'auto',
                allow_dismiss: true
            });

        } else {

            if ($("#clamping_force_e").val()!='') {
                $("#clamping_force").css("border-color","#DFE3E7");
            }

            if ($("#machine_type_e").val()!='') {
                $("#machine_type").css("border-color","#DFE3E7");
            }

            if ($("#screw_type_e").val()!='') {
                $("#screw_type").css("border-color","#DFE3E7");
            }

            if ($("#screw_size_e").val()!='') {
                $("#screw_size").css("border-color","#DFE3E7");
            }

            if ($("input[name=quantity_e]").val()!='') {
                $("input[name=quantity_e]").css("border-color","#DFE3E7");
            }

            $("#editMachineBorchForm").submit();
        }
    });

    $(".deleteProductBrch").bind("click",function(){
        $("#deleteMachineId").val($(this).attr("data-id"));
        $("#deleteMachineModal").modal("show");
    });

    $("#emailSender").tagsinput('items');

    $('#btnAddFile').bind('click',function(e){
        e.preventDefault();
        $("#field_file").trigger('click');
    });

    $('#send2Incomplete_order').bind('click',function(e){
        e.preventDefault();
        var orderID = $('#field_order_id'),
            emailsTo = $('#field_emails_to'),
            emailsBcc = $('#field_emails_bcc'),
            $toMail = $('#toMail');
            orderID.val($('#orderID').val());
            emailsTo.val($toMail.val());
            emailsBcc.val($('#emailSender').val());

        $.bootstrapGrowl("لطفا منتظر بمانید...", {
            type: 'success',
            align: 'center',
            width: 'auto'
        });

        $('#orderTable_form').submit();
    });

    $('.printBtn').on('click', function() {
        $("#printable").print({
            mediaPrint : true, // Add link with attrbute media=print
            stylesheet : "" //Custom stylesheet
        });
    });

    $(".deleteCustomerProduct").bind("click",function(){
        $("#customerProductId").val($(this).attr("data-id"));
        $("#deleteCustomerProductModal").modal("show");
    });

    $('#sendFile').bind('click',function(e){
        e.preventDefault();
        var ext = $('#fileinput_inline').val().split('.').pop().toLowerCase();

        if($.inArray(ext, ['png','jpg','jpeg','doc','docx','xls','xlsx','pdf','ppt','pps','ppsx','pptx']) == -1) {
            $.bootstrapGrowl(
                "فایل انتخابی مجاز نمی باشد، فایل های مجاز : png,jpg,jpeg,pdf,doc,docx,xls,xlsx,ppt,pps,pptx,ppsx", {
                    type: 'danger',
                    align: 'center',
                    width: 'auto',
                    allow_dismiss: true
                });
        } else {
            $.bootstrapGrowl("لطفا منتظر بمانید...", {
                type: 'success',
                align: 'center',
                width: 'auto'
            });

            $('#orderFile_form').submit();
        }
    });

    // setting
    $body.on('click',".deleteScrewType", function () {
        $('#screwTypeId').val($(this).attr('data-id'));
        $('#modalScrewTypeDelete').modal('show');
    });

    $body.on('click',".editScrewType", function () {
        $('#screwTypeIdEdit').val($(this).attr('data-id'));
        $('#clampingForceIdEdit').val($(this).attr('data-clamp'));
        $('#screwTypeNameEdit').val($(this).attr('data-name'));
        $('#screwTypeStatusEdit').val($(this).attr('data-status'));
        $('#ScrewTypeMainDriveEdit').val($(this).attr('data-mainDrive'));

        $('#modalScrewTypeEdit').modal('show');
    });


    $body.on('click',".deleteScrewSize", function () {
        $('#screwSizeId').val($(this).attr('data-id'));
        $('#modalScrewSizeDelete').modal('show');
    });

    $body.on('click',".editScrewSize", function () {
        $('#screwSizeIdEdit').val($(this).attr('data-id'));
        $('#clampingForceIdEdit').val($(this).attr('data-clamp'));
        $('#screwSizeNameAEdit').val($(this).attr('data-namea'));
        $('#screwSizeNameBEdit').val($(this).attr('data-nameb'));
        $('#screwSizeNameCEdit').val($(this).attr('data-namec'));
        $('#screwSizeStatusEdit').val($(this).attr('data-status'));
        $('#ScrewSizeMainDriveEdit').val($(this).attr('data-mainDrive'));

        $('#modalScrewSizeEdit').modal('show');
    });

    $("#checkDate").persianDatepicker();
    $("#deliveryDateInvoice").persianDatepicker();
    $("#delivery").persianDatepicker();

    //not used in template !!!!!!!! 
    $(".a").bind("click",function(){
        $("form[name=deliveryCustomerSale]").children("input[name=invoiceId]").val($(this).attr("data-id"));
        $("#deliveryCustomerSaleModal").modal("show");
    });


    // add skip button to step form
    var skipButton = '';
    skipButton += '<li aria-hidden="true">';
    skipButton +=   '<a class="btn btn-danger" href="http://poolad.dabacenter.ir/index.php">صرف نظر</a>';
    skipButton += '</li>';
    $('ul[aria-label="Pagination"]').prepend(skipButton);

    $("tbody").find('input[type="checkbox"]').click(function(){
        if($(this).prop("checked") == true){
            if (($(this).attr("data-status") == 'sold') || ($(this).attr("data-status") == '3')) {
                $(this).prop("checked",false);
                $.bootstrapGrowl("شما مجاز به انتخاب دستگاه فروخته شده نیستید !", {
                    type: 'danger',
                    align: 'center',
                    width: 'auto'
                });
            }

        }
    });

    $("#clamping_force").change(function(){
        var  mainDrive = $("input[name=main_drive]:checked").val();
        var  clampingForceId = $("#clamping_force option:selected").val();
        var  screwSize = $("#screw_size");
        var  rowsStream = '';
        screwSize.find('option').remove();

        $.ajax({
            url: 'sale.php?action=getScrewSize',
            type: 'post',
            data: {
                mainDrive: mainDrive,
                clampingForceId: clampingForceId
            },
            dataType: 'json',
            success: function (data) {

                rowsStream += '<option value = "' + data["screw_size_id"] + ' ' + data["name_a"] + '">' + data["name_a"] + '</option>';
                rowsStream += '<option value = "' + data["screw_size_id"] + ' ' + data["name_b"] + '" selected>' + data["name_b"] + '</option>';
                rowsStream += '<option value = "' + data["screw_size_id"] + ' ' + data["name_c"] + '">' + data["name_c"] + '</option>';

                screwSize.append(rowsStream);
            }

        });
    });

    $("#clamping_force_e").change(function(){
        var  mainDrive = $("input[name=main_drive_e]:checked").val();
        var  clampingForceId = $("#clamping_force_e option:selected").val();
        var  screwSize = $("#screw_size_e");
        var  rowsStream = '';
        screwSize.find('option').remove();

        $.ajax({
            url: 'sale.php?action=getScrewSize',
            type: 'post',
            data: {
                mainDrive: mainDrive,
                clampingForceId: clampingForceId
            },
            dataType: 'json',
            success: function (data) {

                rowsStream += '<option value = "' + data["screw_size_id"] + ' ' + data["name_a"] + '">' + data["name_a"] + '</option>';
                rowsStream += '<option value = "' + data["screw_size_id"] + ' ' + data["name_b"] + '" selected>' + data["name_b"] + '</option>';
                rowsStream += '<option value = "' + data["screw_size_id"] + ' ' + data["name_c"] + '">' + data["name_c"] + '</option>';

                screwSize.append(rowsStream);
            }

        });
    });

    $("input[name=main_drive]").change(function(){
        if ($("#clamping_force").val() != '') {
            var  mainDrive = $("input[name=main_drive]:checked").val();
            var  clampingForceId = $("#clamping_force option:selected").val();
            var  screwSize = $("#screw_size");
            var  rowsStream = '';
            screwSize.find('option').remove();

            $.ajax({
                url: 'sale.php?action=getScrewSize',
                type: 'post',
                data: {
                    mainDrive: mainDrive,
                    clampingForceId: clampingForceId
                },
                dataType: 'json',
                success: function (data) {

                    rowsStream += '<option value = "' + data["screw_size_id"] + ' ' + data["name_a"] + '">' + data["name_a"] + '</option>';
                    rowsStream += '<option value = "' + data["screw_size_id"] + ' ' + data["name_b"] + '" selected>' + data["name_b"] + '</option>';
                    rowsStream += '<option value = "' + data["screw_size_id"] + ' ' + data["name_c"] + '">' + data["name_c"] + '</option>';

                    screwSize.append(rowsStream);
                }

            });
        }
    });

    $("input[name=main_drive_e]").change(function(){
        if ($("#clamping_force").val() != '') {
            var  mainDrive = $("input[name=main_drive_e]:checked").val();
            var  clampingForceId = $("#clamping_force_e option:selected").val();
            var  screwSize = $("#screw_size_e");
            var  rowsStream = '';
            screwSize.find('option').remove();

            $.ajax({
                url: 'sale.php?action=getScrewSize',
                type: 'post',
                data: {
                    mainDrive: mainDrive,
                    clampingForceId: clampingForceId
                },
                dataType: 'json',
                success: function (data) {

                    rowsStream += '<option value = "' + data["screw_size_id"] + ' ' + data["name_a"] + '">' + data["name_a"] + '</option>';
                    rowsStream += '<option value = "' + data["screw_size_id"] + ' ' + data["name_b"] + '" selected>' + data["name_b"] + '</option>';
                    rowsStream += '<option value = "' + data["screw_size_id"] + ' ' + data["name_c"] + '">' + data["name_c"] + '</option>';

                    screwSize.append(rowsStream);
                }

            });
        }
    });

    $(".deleteMachine").bind("click",function(){
        $("#invoiceDetailId").val($(this).attr("data-id"));
        $("#invoiceId").val($(this).attr("data-idd"));
        $("#deleteMachineModal").modal("show");
    });

    $("#addToInvoiceBtn").bind("click",function(e){
        e.preventDefault();
        var lengthChecked = $("#machineTableInventory input:checked").length;
        if ((lengthChecked == 0)) {
            $.bootstrapGrowl("حداقل باید یکی از دستگاه ها را انتخاب نمایید !", {
                type: 'danger',
                align: 'center',
                width: 'auto',
                allow_dismiss: true
            });
            return false;
        } else if (lengthChecked > 0) {
            if ($("#deliveryTime").val() == '') {
                $.bootstrapGrowl("تاریخ تحویل الزامیست !", {
                    type: 'danger',
                    align: 'center',
                    width: 'auto',
                    allow_dismiss: true
                });
            } else {
                $("#addToInvoiceForm").submit();
            }
        }
    });


    $("#addToInvoiceBtnManual").bind("click",function (e) {
        e.preventDefault();

        if (($("input[name=quantity]").val()=='')||($("#clamping_force").val()=='')||($("#machine_type").val()=='')||($("#screw_type").val()=='')||($("#screw_size").val()=='')||$("#deliveryTime").val()=='') {
            if ($("input[name=quantity]").val()=='') {
                $("input[name=quantity]").css("border-color","crimson");
            }

            if ($("#clamping_force").val()=='') {
                $("#clamping_force").css("border-color","crimson");
            } else {
                $("#clamping_force").css("border-color","#DFE3E7");
            }

            if ($("#machine_type").val()=='') {
                $("#machine_type").css("border-color","crimson");
            } else {
                $("#machine_type").css("border-color","#DFE3E7");
            }

            if ($("#screw_type").val()=='') {
                $("#screw_type").css("border-color","crimson");
            } else {
                $("#screw_type").css("border-color","#DFE3E7");
            }

            if ($("#screw_size").val()=='') {
                $("#screw_size").css("border-color","crimson");
            } else {
                $("#screw_size").css("border-color","#DFE3E7");
            }

            if ($("#deliveryTime").val()=='') {
                $("#deliveryTime").css("border-color","crimson");
            } else {
                $("#deliveryTime").css("border-color","#DFE3E7");
            }

            $.bootstrapGrowl("لطفا فیلدها را پر نمایید.", {
                type: 'danger',
                align: 'center',
                width: 'auto',
                allow_dismiss: true
            });

        } else {
            $("#addToInvoiceFormManual").submit();
        }

    });

});
