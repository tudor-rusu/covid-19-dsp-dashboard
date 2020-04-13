'use strict';

var Document = function () {

    this.doc = new jsPDF({
        orientation: 'portrait',
        unit: 'mm',
        format: 'a4'
    });

    this.reset();
};

Document.prototype.reset = function () {

    this.doc.setFont('TimesNewRoman');
    this.doc.setFontStyle('normal');
    this.doc.setFontSize(10);
    this.doc.setTextColor(0, 0, 0);
};

Document.prototype.download = function (data, signature, qrcode) {

    this.draw();

    this.fill(data);

    this.addSignature(signature);

    this.addQrCode(qrcode);

    this.doc.save(this.getName(data));
};

Document.prototype.getName = function(data) {

    let date = new Date();
    let year = date.getUTCFullYear();
    let month = date.getUTCMonth() + 1; // months from 1-12
    let day = date.getDate();

    if (month <= 9) {

        month = '0' + month;
    }

    return 'declaratie_' + year + month + day + '.pdf';
};

Document.prototype.draw = function () {

    this.doc.rect(10, 10, 190, 10);

    this.doc.setFontStyle('bold');
    this.doc.setFontSize(12);
    this.doc.text('DECLARAȚIE', 105, 16, { align: 'center' });
    
    this.reset();

    // left column
    this.doc.text('Nume', 10, 30);
    this.doc.line(22, 31, 100, 31);

    this.doc.text('Prenume', 10, 35);
    this.doc.line(25, 36, 100, 36);

    this.doc.text('Sexul', 10, 40);
    this.doc.rect(55, 37.2, 3, 3);
    this.doc.text('M', 60, 40);
    this.doc.rect(70, 37.2, 3, 3);
    this.doc.text('F', 75, 40);

    this.doc.text('Pașaport/C.I.', 10, 45);
    this.doc.text('Serie', 35, 45);
    this.doc.line(45, 46, 60, 46);
    this.doc.text('Număr', 67, 45);
    this.doc.line(80, 46, 100, 46);

    this.doc.text('Data nașterii (zi/lună/an)', 10, 50);
    this.doc.line(48, 51, 100, 51);

    this.doc.text('Data sosirii în România', 10, 55);
    this.doc.line(47, 56, 100, 56);

    // right column
    this.doc.text('Țara din care ai plecat', 105, 30);
    this.doc.line(140, 31, 200, 31);

    this.doc.text('Localitate', 105, 35);
    this.doc.line(122, 36, 200, 36);

    this.doc.text('Data', 105, 40);
    this.doc.line(115, 41, 200, 41);

    // table
    this.doc.text('Estimez că voi rămâne în Romania mai mult de 24 de ore la următoarele adrese:', 10, 65);

    this.doc.setFontStyle('bold');

    // table - head
    this.doc.text('Nr. Crt.', 20, 72, { align: 'center' });
    this.doc.text('Locația (oraș)', 52, 72, { align: 'center' });
    this.doc.text('Data sosirii', 87, 72, { align: 'center' });
    this.doc.text('Data plecării', 113, 72, { align: 'center' });
    this.doc.text('Adresa completă', 160, 72, { align: 'center' });

    this.reset();

    // table - horizontal lines
    this.doc.line(10, 74, 200, 74);
    this.doc.line(10, 84, 200, 84);
    this.doc.line(10, 94, 200, 94);

    // contact
    this.doc.text('Pe perioada șederii în Romania, pot fi contactat la:', 10, 110);
    this.doc.text('Nr. Tel.', 10, 115);
    this.doc.line(24, 116, 60, 116);
    this.doc.text('Email', 65, 115);
    this.doc.line(76, 116, 140, 116);

    // questions
    let quesiton1 = '1. Ați locuit/vizitat zone în care se aflau persoane suferinde din cauza infecției cu noul coronavirus (COVID-19)?';
    this.doc.text(quesiton1, 10, 120, { maxWidth: 190 });
    this.doc.rect(10, 122.2, 3, 3);
    this.doc.text('Da', 15, 125);
    this.doc.rect(25, 122.2, 3, 3);
    this.doc.text('Nu', 30, 125);

    let quesiton2 = '2. Ați venit în contact direct cu persoane suferinde din cauza infecției cu noul coronavirus (COVID-19) la serviciu, în vecinătatea locuinței sau vizitând unități medicale ori alte genuri de locuri în ultimele 14 zile?';
    this.doc.text(quesiton2, 10, 130, { maxWidth: 190 });
    this.doc.rect(10, 137.2, 3, 3);
    this.doc.text('Da', 15, 140);
    this.doc.rect(25, 137.2, 3, 3);
    this.doc.text('Nu', 30, 140);

    let quesiton3 = '3. Ați fost spitalizat în ultimele trei săptămâni?';
    this.doc.text(quesiton3, 10, 145, { maxWidth: 190 });
    this.doc.rect(10, 147.2, 3, 3);
    this.doc.text('Da', 15, 150);
    this.doc.rect(25, 147.2, 3, 3);
    this.doc.text('Nu', 30, 150);

    let quesiton4 = '4. Ați avut una sau mai multe dintre urmatoarele simptome?';
    this.doc.text(quesiton4, 10, 155, { maxWidth: 190 });

    this.doc.text('- Febră', 10, 160);
    this.doc.rect(50, 157.2, 3, 3);
    this.doc.text('Da', 55, 160);
    this.doc.rect(65, 157.2, 3, 3);
    this.doc.text('Nu', 70, 160);

    this.doc.text('- Dificultatea de a înghiți', 10, 165);
    this.doc.rect(50, 162.2, 3, 3);
    this.doc.text('Da', 55, 165);
    this.doc.rect(65, 162.2, 3, 3);
    this.doc.text('Nu', 70, 165);

    this.doc.text('- Dificultatea de a respira', 10, 170);
    this.doc.rect(50, 167.2, 3, 3);
    this.doc.text('Da', 55, 170);
    this.doc.rect(65, 167.2, 3, 3);
    this.doc.text('Nu', 70, 170);

    this.doc.text('- Tuse intensă', 10, 175);
    this.doc.rect(50, 172.2, 3, 3);
    this.doc.text('Da', 55, 175);
    this.doc.rect(65, 172.2, 3, 3);
    this.doc.text('Nu', 70, 175);

    // bigtext
    let text1 = 'Aviz important și în acord: În contextul evoluțiilor înregistrate începând cu ianuarie 2020 în legatură cu infecția cu noul coronavirus COVID-19, pentru a putea rămâne în România, toți pasagerii din sau care au călătorit recent în China, Italia, Coreea de Sud, Iran, sunt obligați să completeze chestionarul de mai sus. Vă rugăm să rețineți că datele și informațiile furnizate aici sunt solicitate pentru colectare și pentru prelucrare de către Direcția de Sănătate Publică Județeană _____________________________________________. Datele și informațiile solicitate și colectate sunt prelucrate în conformitate cu prevederile Regulamentului nr. 679/2016 privind protecția persoanelor fizice în ceea ce privește prelucrarea datelor cu caracter personal și libera circulație a acestor date, cu respectarea strictă a principiilor legate de drepturile fundamentale. Persoanele ale căror date sunt prelucrate beneficiază de dreptul de a-și exercita drepturile de modificare, intervenție și opoziție printr-o cerere semnată, datată și scrisă adresată operatorului de date.';
    this.doc.text(text1, 10, 180, { maxWidth: 190 });

    let text2 = '* Sunt conștient că un refuz de a completa chestionarul poate provoca refuzul intrării mele pe teritoriul României, în scopul eliminării eventualelor amenințări la adresa sănătății publice a României.';
    this.doc.text(text2, 10, 215, { maxWidth: 190 });

    let text3 = '* Cunoscând prevederile art. 326 din Codul Penal cu privire la falsul în declarații și art. 352 din Codul Penal cu privire la zădărnicirea combaterii bolilor, declar prin prezenta, pe propria răspundere, că am sosit pe teritoriul României plecând din țara de origine, cu tranzitarea teritoriului următoarelor țări: _________________________________________________________________ și că voi urma indicațiile personalului medical care mi-au fost aduse la cunoștiință pe timpul efectuării controlului de frontieră în punctul de trecere a frontierei _____________________________________________.';
    this.doc.text(text3, 10, 225, { maxWidth: 190 });

    let text4 = '* Declar pe propria răspundere faptul că, pentru a preveni răspândirea pe teritoriul României a virusului COVID-19, după părăsirea perimetrului punctului de trecere a frontierei mă voi deplasa la ________________________________________________________, pentru auto-izolare sau plasare în carantină, folosind _____________________ urmând traseul următor: ___________________________________________________________________________________________________________.';
    this.doc.text(text4, 10, 245, { maxWidth: 190 });

    let text5 = '* Sunt de acord că informațiile furnizate pot fi consultate și prelucrate de către autoritățile competente.';
    this.doc.text(text5, 10, 265, { maxWidth: 190 });

    // date
    this.doc.text('Data și locul', 50, 275);
    this.doc.text('Semnătura', 140, 275);
};

Document.prototype.fill = function (data) {

    // left column
    this.doc.text(data.lastName, 65, 30, { align: 'center' });
    this.doc.text(data.firstName, 65, 35, { align: 'center' });
    this.doc.text('X', data.sex === 'M' ? 56.5 : 71.5, 40, { align: 'center' });
    this.doc.text(data.idCardSeries, 53, 45, { align: 'center' });
    this.doc.text(data.idCardNumber, 90, 45, { align: 'center' });
    this.doc.text(data.birthday, 75, 50, { align: 'center' });
    this.doc.text(data.dateArrival, 75, 55, { align: 'center' });

    // right column
    this.doc.text(data.countryLeave, 170, 30, { align: 'center' });
    this.doc.text(data.localityLeave, 170, 35, { align: 'center' });
    this.doc.text(data.dateLeave, 170, 40, { align: 'center' });

    // table
    data.addresses.forEach(function (address, index) {

        let height = (index+1)*10;

        this.doc.text(index+1+'', 20, 70 + height, { align: 'center' });
        this.doc.text(address.locality, 52, 70 + height, { align: 'center' });
        this.doc.text(address.dateArrival, 87, 70 + height, { align: 'center' });
        this.doc.text(address.dateLeave, 113, 70 + height, { align: 'center' });
        this.doc.text(address.fullAddress, 160, 68 + height, { align: 'center', maxWidth: 65 });

    }.bind(this));

    // contact
    this.doc.text(data.phoneNumber, 43, 115, { align: 'center' });
    this.doc.text(data.emailAddress, 106, 115, { align: 'center' });

    // questions
    this.doc.text('X', data.answers.hasVisited ? 10 : 25, 125);
    this.doc.text('X', data.answers.hasContacted ? 10 : 25, 140);
    this.doc.text('X', data.answers.isHospitalized ? 10 : 25, 150);
    this.doc.text('X', data.answers.hasFever ? 50 : 65, 160);
    this.doc.text('X', data.answers.hasDifficultySwallow ? 50 : 65, 165);
    this.doc.text('X', data.answers.hasDifficultyBreath ? 50 : 65, 170);
    this.doc.text('X', data.answers.hasIntenseCough ? 50 : 65, 175);

    // bigtext
    this.doc.text(data.organization, 159, 192, { align: 'center' });
    this.doc.text(data.visitedCountries.join(', '), 125, 233, { align: 'center' });
    this.doc.text(data.borderCrossingPoint, 75, 241, { align: 'center' });
    this.doc.text(data.destination, 145, 249, { align: 'center' });
    this.doc.text(data.vehicle, 103, 253, { align: 'center' });
    this.doc.text(data.route, 100, 257, { align: 'center' });
    this.doc.text(data.documentDate + ', ' + data.documentLocality, 59, 280, { align: 'center' });
};

Document.prototype.addSignature = function (signature) {

    this.doc.addImage(signature, 'PNG', 155, 260, 40, 30);
};

Document.prototype.addQrCode = function (qrcode) {

    this.doc.addImage(qrcode, 'PNG', 185, 45, 15, 15);
};