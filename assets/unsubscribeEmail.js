$(function () {
    $(".unsub").off("click").on("click", function () {
        let motivo = $(this).attr("rel");
        let email = $("#email").val();
        toast("Enviando", 1500, "toast-warning");
        post('email-control', 'email/unsubscribe', {motivo: motivo, email: email}, function () {
            toast("Email Removido", 2000, "toast-success");
            setTimeout(function () {
                location.href = HOME;
            }, 3000);
        });
    });

    $("#save").off("click").on("click", function () {
        let assuntos = [];
        if ($("#promo").prop("checked"))
            assuntos.push(1);
        if ($("#artigos").prop("checked"))
            assuntos.push(2);
        if ($("#outros").prop("checked"))
            assuntos.push(3);

        let frequencia = $('input[name=frequencia]:checked').val();
        let email = $("#email").val();
        toast("Enviando", 1500, "toast-warning");

        post('email-control', 'email/preferences', {
            assuntos: assuntos,
            frequencia: frequencia,
            email: email
        }, function () {
            toast("Preferências Atualizadas!", 2000, "toast-success");
            setTimeout(function () {
                location.href = HOME;
            }, 2000);
        });
    });
});