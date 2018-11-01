$(function() {
   $(".unsub").off("click").on("click", function () {
       let motivo = $(this).attr("rel");
       let email = $("#email").val();
       post('email-control', 'email/unsubscribe', {motivo: motivo, email: email}, function (g) {
           if(g) {
               toast("Email Removido", 3000, "toast-success");
               setTimeout(function () {
                   location.href = HOME;
               }, 3000);
           }
       });
   });

   $("#save").off("click").on("click", function () {
       let assuntos = [];
       if($("#promo").prop("checked"))
           assuntos.push(2);
       if($("#artigos").prop("checked"))
           assuntos.push(3);
       if($("#outros").prop("checked"))
           assuntos.push(4);

       let frequencia = $(".frequencia").val();

       post('email-control', 'email/preferences', {assuntos: assuntos, frequencia: frequencia}, function (g) {
           if(g) {
               toast("Email Removido", 3000, "toast-success");
               setTimeout(function () {
                   location.href = HOME;
               }, 3000);
           }
       });
   });
});