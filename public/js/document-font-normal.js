﻿(function (jsPDFAPI) {
var callAddFont = function () {
this.addFileToVFS('TimesNewRoman-normal.ttf', font);
this.addFont('TimesNewRoman-normal.ttf', 'TimesNewRoman', 'normal');
};
jsPDFAPI.events.push(['addFonts', callAddFont])
 })(jsPDF.API);