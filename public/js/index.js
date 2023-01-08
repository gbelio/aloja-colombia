const publicKey = document.getElementById("mercado-pago-public-key").value;
const mercadopago = new MercadoPago(publicKey);
function loadCardForm() {
    const productDescription = document.getElementById('booking-id').value;
    const productCost = document.getElementById('amount').value.split('.').join("");
    const cardForm = mercadopago.cardForm({
        amount: productCost,
        autoMount: true,
        form: {
            id: "form-checkout",
            cardholderName: {
                id: "form-checkout__cardholderName",
                placeholder: "Nombre Completo",
            },
            cardholderEmail: {
                id: "form-checkout__cardholderEmail",
                placeholder: "E-mail",
            },
            cardNumber: {
                id: "form-checkout__cardNumber",
                placeholder: "Número de Tarjeta",
            },
            cardExpirationMonth: {
                id: "form-checkout__cardExpirationMonth",
                placeholder: "MM",
            },
            cardExpirationYear: {
                id: "form-checkout__cardExpirationYear",
                placeholder: "AA",
            },
            securityCode: {
                id: "form-checkout__securityCode",
                placeholder: "Código de Seg.",
            },
            installments: {
                id: "form-checkout__installments",
                placeholder: "Cuotas",
            },
            identificationType: {
                id: "form-checkout__identificationType",
            },
            identificationNumber: {
                id: "form-checkout__identificationNumber",
                placeholder: "Número de identificación",
            },
            issuer: {
                id: "form-checkout__issuer",
                placeholder: "Issuer",
            },
        },
        callbacks: {
            onFormMounted: error => {
                if (error)
                    return console.warn("Form Mounted handling error: ", error);
                console.log("Form mounted");
            },
            onSubmit: event => {
                event.preventDefault();
                document.getElementById("loading-message").style.display = "block";
                modal('on');
                const {
                    paymentMethodId,
                    issuerId,
                    cardholderEmail: email,
                    amount,
                    token,
                    installments,
                    identificationNumber,
                    identificationType,
                } = cardForm.getCardFormData();

                fetch("/card_process_payment", {
                    method: "POST",
                    credentials: "same-origin",
                    dataType: "json",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                        "X-CSRF-Token": $('input[name="_token"]').val()
                    },
                    body: JSON.stringify({
                        token,
                        issuerId,
                        paymentMethodId,
                        transactionAmount: Number(document.getElementById('amount').value.split('.').join("")),
                        installments: Number(1),
                        description: productDescription,
                        payer: {
                            email,
                            identification: {
                                type: identificationType,
                                number: identificationNumber,
                            },
                        },
                    }),
                })
                    .then(response => {
                        return response.json();
                    })
                    .then(result => {
                        modal('off');
                        document.getElementById("success-response").style.display = "block";
                        document.getElementById("payment-id").innerText = result.id;
                        console.log("estado: ",result.status);
                        console.log("detalle: ",result.detail);
                        if(result.status == "approved"){
                            document.getElementById("payment-status").innerText = "Felicitaciones, pago aprobado.";
                            document.getElementById("payment-detail").innerText = "Revisa tu correo para recibir más instrucciones.";
                        }else if(result.status == "undefined"){
                            document.getElementById("error-message").textContent = "Ha habido un error, vuelve a intentarlo";
                            document.getElementById("fail-response").style.display = "block";
                        }else {
                            console.log("estado: ",result.status);
                            document.getElementById("payment-status").innerText = "Lamentablemente el pago no pudo realizarse.";
                            switch (result.detail) {
                                case 'cc_rejected_other_reason':
                                    document.getElementById("payment-detail").innerText = "Rechazado por error general.";
                                    break;
                                case 'pending_contingency':
                                    document.getElementById("payment-detail").innerText = "Pendiente de pago.";
                                    break;
                                case 'cc_rejected_call_for_authorize':
                                    document.getElementById("payment-detail").innerText = "Rechazado, requiere autorizar pago.";
                                    break;
                                case 'cc_rejected_insufficient_amount':
                                    document.getElementById("payment-detail").innerText = "Rechazado por importe insuficiente.";
                                    break;
                                case 'cc_rejected_bad_filled_security_code':
                                    document.getElementById("payment-detail").innerText = "Rechazado por código de seguridad inválido.";
                                    break;
                                case 'cc_rejected_bad_filled_date':
                                    document.getElementById("payment-detail").innerText = "Rechazado debido a un problema de fecha de vencimiento.";
                                    break;
                                case 'cc_rejected_bad_filled_other':
                                    document.getElementById("payment-detail").innerText = "Revise los campos completados.";
                                    break;
                                default:
                                    break;
                            }
                        }
                        $('.container__payment').fadeOut(500);
                        setTimeout(() => { $('.container__result').show(500).fadeIn(); }, 500);
                        if(result.status == "approved") {
                            document.getElementById("reload").style.display = "none";
                            document.getElementById("back-url").style.display = "block";
                        }else{
                            document.getElementById("reload").style.display = "block";
                            document.getElementById("back-url").style.display = "none";
                        }
                    })
                    .catch(error => {
                        alert("Unexpected error\n"+error);
                    });
                    modal('off');
            },
            onFetching: (resource) => {
                const payButton = document.getElementById("form-checkout__submit");
                payButton.setAttribute('disabled', true);  
                return () => {
                    payButton.removeAttribute("disabled");
                };
            },
        },
    });
};

document.getElementById('reload').addEventListener('click', function(){
    console.log("RELOAD");
        window.location.reload();
});

// Handle transitions
document.onload = function(){
    setTimeout(() => {
                $('.container__payment').show(500).fadeIn();
        }, 500);
}

document.getElementById('go-cash-payment').addEventListener('click', function(){
    $('.container__payment').fadeOut(300);
    setTimeout(() => {
        loadCardForm();
        $('.container__payment-cash').show(500).fadeIn(); 
    }, 100);
});

document.getElementById('go-card-payment').addEventListener('click', function(){
    $('.container__payment-cash').fadeOut(300);
    setTimeout(() => {
        $('.container__payment').show(500).fadeIn();
    }, 100);
});

//split de pagos
document.getElementById('split-1').addEventListener('click', function(){
    let amount = Number(document.getElementById('sum-total').value.split('.').join(""))/1;
    document.getElementById('amount').value.split('.').join("");
    document.getElementById('amount').value = amount;
    document.getElementById('1-pago').style.display = "block";
    document.getElementById('2-pago').style.display = "none";
    document.getElementById('1-pago').innerText = "Un pago de: $ " + amount;
});

document.getElementById('split-cash-1').addEventListener('click', function(){
    let amount = Number(document.getElementById('sum-total').value.split('.').join(""))/1;
    document.getElementById('amount-cash').value = amount;
    document.getElementById('1-pago-cash').style.display = "block";
    document.getElementById('2-pago-cash').style.display = "none";
    document.getElementById('1-pago-cash').innerText = "Dos pagos de: $ " + amount;
});

document.getElementById('split-2').addEventListener('click', function(){
    let amount = Number(document.getElementById('sum-total').value.split('.').join(""))/2;
    document.getElementById('amount').value = amount;
    document.getElementById('2-pago').style.display = "block";
    document.getElementById('1-pago').style.display = "none";
    document.getElementById('2-pago').innerText = "Dos pagos de: $ " + amount;
});

document.getElementById('split-cash-2').addEventListener('click', function(){
    let amount = Number(document.getElementById('sum-total').value.split('.').join(""))/2;
    document.getElementById('amount-cash').value = amount;
    document.getElementById('2-pago-cash').style.display = "block";
    document.getElementById('1-pago-cash').style.display = "none";
    document.getElementById('2-pago-cash').innerText = "Dos pagos de: $ " + amount;
});

//Revisa si los días restantes son más de 30 y lo deja pagar en dos veces.
function showSplit(){
    document.getElementById('split-1').checked = "true";
    document.getElementById('split-cash-1').checked = "true";
    const date_1 = new Date(document.getElementById('arrival-date').value);
    const date_2 = new Date(document.getElementById('confirmation-date').value);
    const difference = date_1.getTime() - date_2.getTime();
    const totalDays = Math.ceil(difference / (1000 * 3600 * 24));
    const payStatus = document.getElementById('pay-status').value;
    if(totalDays > 30 || payStatus == "PP"){
        document.getElementById('split-pagos-cash').style.display = "block";
        document.getElementById('split-pagos').style.display = "block";
        if (payStatus == "PP") {
            let amount = Number(document.getElementById('sum-total-mitad').value.split('.').join(""));
            document.getElementById('amount').value = amount;
            document.getElementById('amount-cash').value = amount;
            document.getElementById('1-pago').style.display = "none";
            document.getElementById('1-pago-cash').style.display = "none";
            document.getElementById('2-pago').innerText = "Dos pagos de: $ " + amount;
            document.getElementById('2-pago-cash').innerText = "Dos pagos de: $ " + amount;
            document.getElementById('split-cash-1').disabled = "true";
            document.getElementById('split-cash-2').checked = "true";
            document.getElementById('split-cash-2').disabled = "true";
            document.getElementById('split-1').disabled = "true";
            document.getElementById('split-2').checked = "true";
            document.getElementById('split-2').disabled = "true";
        }
    }
    console.log('split: ', document.getElementById('split-pagos').value);
    console.log('pay-status: ', document.getElementById('pay-status').value);
    console.log('total days: ', totalDays);
};
//fin split de pagos

// Handle price update
function updatePrice(){
    let amount = Number(document.getElementById('sum-total').value.split('.').join(""));
    document.getElementById('1-pago').innerText = "Un pago de: $ " + amount;
    document.getElementById('1-pago-cash').innerText = "Un pago de: $ " + amount;
    document.getElementById('amount').value = amount;
    document.getElementById('amount-cash').value = amount;
};

document.getElementById('paymentForm').addEventListener('submit', function(){
    modal('on');
    }
);

function modal(action){
    if(action === 'on')
        $('.modal').modal('show');
    else
        $('.modal').modal('hide');
};
updatePrice();
showSplit();
loadCardForm();
