switch (state) {
	case 0: // i = 0
		i = 0;
		display.setValue("i", i);
		state = 1;
		break;

	case 1: // i < len
		if (i < len) {
			state = 3;
		} else {
			state = 12;
		}
		display.compare("i", "len");
		break;

	case 2: // i++
		i++;
		display.countOps();
		display.setValue("i", i);
		state = 1;
		break;

	case 3: // min = i
		min = i;
		display.setValue("min", i);
		state = 4;
		break;

	case 4: // j = i + 1
		j = i + 1;
		display.countOps();
		display.setValue("j", j);
		state = 5;
		break;

	case 5: // j < len
		if (j < len) {
			state = 7;
		} else {
			state = 9;
		}
		display.compare("j", "len");
		break;

	case 6: // j++
		j++;
		display.countOps();
		display.setValue("j", j);
		state = 5;
		break;

	case 7: // a[j] < a[min]
		if (a[j] < a[min]) {
			state = 8;
		} else {
			state = 6;
		}
		display.compare("a" + j, "a" + min);
		break;

	case 8: // min = j
		min = j;
		display.setValue("min", j);
		state = 6;
		break;

	case 9: // t = a[min]
		t = a[min];
		display.setValue("t", a[min]);
		state = 10;
		break;

	case 10: // a[min] = a[i]
		a[min] = a[i];
		display.setValue("a" + min, a[i]);
		state = 11;
		break;

	case 11: // a[i] = t
		a[i] = t;
		display.setValue("a" + i, t);
		state = 2;
		break;

	case 12: // DONE
		done = true;
		pause();
		ctrl.set(ctrl.END);
		break;
		
	default:
		alert("ERROR: undefined state: " + state);
	}