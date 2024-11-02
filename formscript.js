	var vtipo = document.getElementById("stipo");
	var vkey = document.getElementById("pixkey");
	
	function TestaCPF(strCPF) {
		var Soma;
		var Resto;
		Soma = 0;
		if (strCPF == "00000000000") return false;

		for (i=1; i<=9; i++) Soma = Soma + parseInt(strCPF.substring(i-1, i)) * (11 - i);
		Resto = (Soma * 10) % 11;

		if ((Resto == 10) || (Resto == 11))  Resto = 0;
		if (Resto != parseInt(strCPF.substring(9, 10)) ) return false;

		Soma = 0;
		for (i = 1; i <= 10; i++) Soma = Soma + parseInt(strCPF.substring(i-1, i)) * (12 - i);
		Resto = (Soma * 10) % 11;

		if ((Resto == 10) || (Resto == 11))  Resto = 0;
		if (Resto != parseInt(strCPF.substring(10, 11) ) ) return false;
		return true;
	}
	
	function TestaCNPJ(cnpj) {
     
    if (cnpj == "00000000000000" || 
        cnpj == "11111111111111" || 
        cnpj == "22222222222222" || 
        cnpj == "33333333333333" || 
        cnpj == "44444444444444" || 
        cnpj == "55555555555555" || 
        cnpj == "66666666666666" || 
        cnpj == "77777777777777" || 
        cnpj == "88888888888888" || 
        cnpj == "99999999999999")
        return false;
         
    tamanho = cnpj.length - 2;
    numeros = cnpj.substring(0,tamanho);
    digitos = cnpj.substring(tamanho);
    soma = 0;
    pos = tamanho - 7;
    for (i = tamanho; i >= 1; i--) {
      soma += numeros.charAt(tamanho - i) * pos--;
      if (pos < 2)
            pos = 9;
    }
    resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
    if (resultado != digitos.charAt(0))
        return false;
         
    tamanho = tamanho + 1;
    numeros = cnpj.substring(0,tamanho);
    soma = 0;
    pos = tamanho - 7;
    for (i = tamanho; i >= 1; i--) {
      soma += numeros.charAt(tamanho - i) * pos--;
      if (pos < 2)
            pos = 9;
    }
    resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
    if (resultado != digitos.charAt(1))
          return false;
           
    return true;   
	}
	
	function checkVal() {
		if (vtipo.value ==	"3")
			if (!(TestaCPF(vkey.value))) {
				alert("CPF Inválido!");
				vkey.value = "";
			}
		
		if (vtipo.value ==	"4")
			if (!(TestaCNPJ(vkey.value))) {
				alert("CNPJ Inválido!");
				vkey.value = "";
			}
	}
		
	function setTipo() { 
		vkey.value = "";
		switch (vtipo.value) {
			case "1":
				vkey.pattern ="[^@\s]+@[^@\s]+\.[^@\s]+"; 
				vkey.title="Endereço de E-Mail";
				vkey.placeholder="exemplo@email.com";
			break;
			case "2":
				vkey.pattern ="^[+][0-9]{13}$";
				vkey.title="Celular com código do país (+55 = Brasil) e DDD, apenas os numeros";
				vkey.placeholder="+5511222222222";
				vkey.value = "+55";
			break;
			case "3":
				vkey.pattern ="^[0-9]{11}$";
				vkey.title="CPF, apenas os numeros";
				vkey.placeholder="99999999900";
			break;
			case "4":
				vkey.pattern ="^[0-9]{14}$";
				vkey.title="CNPJ, apenas os numeros";
				vkey.placeholder="99999999000199";
			break;
			case "5":
				vkey.pattern ="^[0-9a-zA-Z]{8}-[0-9a-zA-Z]{4}-[0-9a-zA-Z]{4}-[0-9a-zA-Z]{4}-[0-9a-zA-Z]{12}$";
				vkey.title="Chave Aleatória";
				vkey.placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx";
			break;
		}
	}