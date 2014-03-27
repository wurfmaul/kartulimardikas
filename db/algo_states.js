switch (state) {
	case 0: // i = 0
		i = 0;
		state = 1;
		$("#btn-i").prop("value", 0);
		countWrite();
		break;

	case 1: // i < len
		if (i < len) {
			state = 3;
		} else {
			state = 12;
		}
		countCompare();
		break;

	case 2: // i++
		i++;
		$("#btn-i").prop("value", i);
		countOps();
		countWrite();
		state = 1;
		break;

	case 3: // min = i
		min = i;
		$("#btn-min").prop("value", i);
		countWrite();
		state = 4;
		break;

	case 4: // j = i + 1
		j = i + 1;
		$("#btn-j").prop("value", j);
		countWrite();
		countOps();
		state = 5;
		break;

	case 5: // j < len
		if (j < len) {
			state = 7;
		} else {
			state = 9;
		}
		countCompare();
		break;

	case 6: // j++
		j++;
		$("#btn-j").prop("value", j);
		countOps();
		countWrite();
		state = 5;
		break;

	case 7: // a[j] < a[min]
		if (a[j] < a[min]) {
			state = 8;
		} else {
			state = 6;
		}
		countCompare();
		break;

	case 8: // min = j
		min = j;
		$("#btn-min").prop("value", j);
		countWrite();
		state = 6;
		break;

	case 9: // t = a[min]
		t = a[min];
		$("#btn-t").prop("value", a[min]);
		countWrite();
		state = 10;
		break;

	case 10: // a[min] = a[i]
		a[min] = a[i];
		$("#btn-a" + min).prop("value", a[i]);
		countWrite();
		state = 11;
		break;

	case 11: // a[i] = t
		a[i] = t;
		$("#btn-a" + i).prop("value", t);
		countWrite();
		state = 2;
		break;

	case 12: // DONE
		done = true;
		ctrl.set(ctrl.END);
		break;
		
	default:
		alert("ERROR: undefined state: " + state);
	}