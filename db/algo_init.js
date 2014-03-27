a = shuffle(a);
	len = a.length;
	i = 0;
	min = 0;
	j = 0;
	t = 0;
	for (var i = 0; i < len; i++) {
		$("#btn-a" + i).prop("value", a[i]);
	}
	$("#btn-len").prop("value", len);
	$("#btn-i").prop("value", "?");
	$("#btn-min").prop("value", "?");
	$("#btn-j").prop("value", "?");
	$("#btn-t").prop("value", "?");