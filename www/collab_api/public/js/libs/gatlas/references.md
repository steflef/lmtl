### Iteration on modules


<code>
parseEvents = function (e, l, onComplete) {

    var pos = 0;
    var step = 50;
    var l_up = this.aLookup;

    (function () {
        //console.info("// BATCH ("+pos+")//");
        var s = pos + step;
        for (var i = pos; i < s; i++) {
            if (i >= l) break;

            // DO SOME STUFF
        }

        pos = s;
        //console.log("pos = "+pos);

        if (pos < l) {
            // DO UPDATE STATUS
            setTimeout(arguments.callee, 10);
        } else {
            // DO UPDATE STATUS
            //console.info(" /// PARSER END ///");
            onComplete();
        }
    })();
}
</code>