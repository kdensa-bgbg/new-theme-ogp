var mainColor = "ffcc00"; // here you can set Default Theme Color HEX (without #)
var PickerTheme = "dark"; // there are 2 Themes for Color Picker: light and dark
eval(
  (function () {
    var q = [
      80, 81, 60, 75, 74, 88, 87, 82, 86, 94, 71, 90, 66, 76, 65, 72, 70, 79,
      89, 85,
    ];
    var a = [];
    for (var z = 0; z < q.length; z++) a[q[z]] = z + 1;
    var x = [];
    for (var p = 0; p < arguments.length; p++) {
      var u = arguments[p].split("~");
      for (var o = u.length - 1; o >= 0; o--) {
        var f = null;
        var r = u[o];
        var n = null;
        var v = 0;
        var c = r.length;
        var l;
        for (var w = 0; w < c; w++) {
          var i = r.charCodeAt(w);
          var k = a[i];
          if (k) {
            f = (k - 1) * 94 + r.charCodeAt(w + 1) - 32;
            l = w;
            w++;
          } else if (i == 96) {
            f =
              94 * (q.length - 32 + r.charCodeAt(w + 1)) +
              r.charCodeAt(w + 2) -
              32;
            l = w;
            w += 2;
          } else {
            continue;
          }
          if (n == null) n = [];
          if (l > v) n.push(r.substring(v, l));
          n.push(u[f + 1]);
          v = w + 1;
        }
        if (n != null) {
          if (v < c) n.push(r.substring(v));
          u[o] = n.join("");
        }
      }
      x.push(u[0]);
    }
    var s = x.join("");
    var j = "abcdefghijklmnopqrstuvwxyz";
    var y = [39, 96, 126, 10, 42, 92].concat(q);
    var t = String.fromCharCode(64);
    for (var z = 0; z < y.length; z++)
      s = s.split(t + j.charAt(z)).join(String.fromCharCode(y[z]));
    return s.split(t + "!").join(t);
  })(
    'var _$_7a96=[" #datetime, .datetime > ulP" tPH tr .PHerSortDownP" tPH tr .PHerSort@zpP" tPH tr thP" tfoot tr th, h4, .dragbox h4.collapse, .login_title, h2"," ",""," .main-content"," .login_buP8, .monitorPB tr tdP= inputP#, PB, .administration-PBs div:hover"," #content a:link, #content a:visited, inputP#P= buP8, .login_buP8",".menu > ul > li > aP= .menu > ul > li:hover > a, .user_menu_link_selected, .admin_menu_link_selected"," tr.maintr.even, tr.maintr.odd, tr.expand-child.odd, tr.expand-child.even","P/text@f"]:focus,P/password@f"]:focus,textarea:focus,P/text@f"]P=P/password@f"]P=textareaP=inputP#P=buP8, inputP#, PBP"P" tfoot td","keyup","value","colpickSetCPF","bind","customP;cookie","fadeIn","fade@xut","val","backgroundCPF","#","css","background-P;P;P>left-P;P>top-P;@istyle>","{P6;}","{background-P6 !important;}","{P>left-P6{P>bottom-P6{P>P6!important;}@i/style>","append","PH","/","colpick","#cPFpicker","click",".reset_hex","ready"];j@huery(document)[P:41]](function($){var dP90P?eP91P?bP92P?cP93P?fP94P?jP95P?iP96P?hP97P?gP98];$(P:38])[P:37]]({cPF:(mainCPF),cPFScheme:(@gickerTheme),on@seforeShowP7){$(thisP41]]($P<P$))},onShowP7k){$(kP45]](500);return false},on@videP7k){$(kP46]](500);return false},onSubmitP7l,m,n,o){$(oP47]](mPDoPEP 18P,m);$PAP 21P,mPDePEP 22P,mPDbPEP 23P,mPDcPEP 24P,mPDPGP-P*P&]P02P)]P029]+ P1]P029P+]P02P(]P0PI;$P<P$,m,{expires:365,path:P:36]})},onChangeP7l,o,n){$PAP 21P,oPDePEP 22P,oPDbPEP 23P,oPDcPEP 24P,oPDPGP-P*P&P.2P)P.29]+ P1P.29P+P.2P(P.PI}}P42]](P:9],function(){$(thisP41]](this[P:10]])});if(($P<P$)!= null)){$PAP 21P,$P<P$)PDePEP 22P,$P<P$)PDbPEP 23P,$P<P$)PDcPEP 24P,$P<P$)PDPGP-P*P&P5P$P3P)P5P$)+ P:29]+ P1P5P$)+ P:29P+P5P$P3P(P5P$)+ P:33]PDP:38]P47]]($P<P$))}else {$PAP 21P2P%ePEP 22P2P%bPEP 23P2P%PCP 24P2P%PGP-P*P&P!P@P)P!P@9]+ P1P!P@9P+P!P@P(P!7a96[PI};$(P:40])[P:39]](function(){$PAP 21P2P%ePEP 22P2P%bPEP 23P2P%PCP 24P2P%PGP-P*P&P!P@P)P!P@9]+ P1P!P@9P+P!P@P(P!7a96[PI;$P<P$,(mainCPF),{expires:365,path:P:36]})})})~_7a96[20]](P:~]+ (mainCPF)+ _$_~, table.tablesorter~[type=@f"submit@f"]~6[14]](P:13]~]+ (mainColor)PD~5]+ j+ P:26~P*5~9]+ g+ P:32~7]+ f+ P:28~6[34]](P:2~]+ h+ P:31~P2]+ ~a96[35])P<~]+ o+ P:~input[type=@f"~+ m+ P:~i+ P:30~],P:19~)+ P:2~)[P:1~]+ $P<~cPF:#","~:function(~tton:hover~=P:~PGa96[~cPF","~[PGa9~:hover,~border-~];var ~7a96[2~(dPE~button~cPE~);$(~)[_$~olor~_$_7~head~33])'
  )
);
