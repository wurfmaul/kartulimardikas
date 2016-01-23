INSERT INTO `algorithm` (`aid`, `uid`, `name`, `description`, `long_description`, `variables`, `tree`, `date_creation`, `date_publish`) VALUES
  (1, 1, 'Bubble Sort', '', 'https://de.wikipedia.org/wiki/Bubblesort\n\n```\nBubblesort(A)\n  for (n=A.length; n>1; n=n-1)\n    for (i=0; i<n-1; i=i+1)\n      if (A[i] > A[i+1])\n        swap(A[i], A[i+1])\n```', '{"0":{"n":"a","t":"[i","v":"R","s":9},"1":{"n":"n","t":"i","v":"?","s":2},"2":{"n":"i","t":"i","v":"?","s":2}}', '[{"i":"0","n":"bk"},{"i":"1","n":"as","t":{"k":"v","i":"1"},"f":"0","v":{"k":"p","i":"0","p":"length"}},{"i":"2","n":"cp","l":{"k":"v","i":"1"},"r":{"k":"c","t":"i","v":"1"},"o":"gt"},{"i":"3","n":"bk","c":["2"]},{"i":"4","n":"bk"},{"i":"5","n":"as","t":{"k":"v","i":"2"},"f":"4","v":{"k":"c","t":"i","v":"0"}},{"i":"6","n":"cp","l":{"k":"v","i":"2"},"r":{"k":"e","l":{"k":"v","i":"1"},"r":{"k":"c","t":"i","v":"1"},"o":"-"},"o":"lt"},{"i":"7","n":"bk","c":["6"]},{"i":"8","n":"cp","l":{"k":"i","i":"0","x":{"k":"v","i":"2"}},"r":{"k":"i","i":"0","x":{"k":"e","l":{"k":"v","i":"2"},"r":{"k":"c","t":"i","v":"1"},"o":"+"}},"o":"gt"},{"i":"9","n":"bk","c":["8"]},{"i":"10","n":"sw","l":{"k":"i","i":"0","x":{"k":"v","i":"2"}},"r":{"k":"i","i":"0","x":{"k":"e","l":{"k":"v","i":"2"},"r":{"k":"c","t":"i","v":"1"},"o":"+"}}},{"i":"11","n":"bk","c":["10"]},{"i":"12","n":"bk"},{"i":"13","n":"if","c":"9","b":"11","e":"12","o":"l"},{"i":"14","n":"ic","v":{"k":"v","i":"2"},"o":"i"},{"i":"15","n":"bk","c":["13","14"]},{"i":"16","n":"wl","c":"7","b":"15","o":"l"},{"i":"17","n":"ic","v":{"k":"v","i":"1"},"o":"d"},{"i":"18","n":"bk","c":["5","16","17"]},{"i":"19","n":"wl","c":"3","b":"18","o":"l"},{"i":"20","n":"bk","c":["1","19"]}]', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
  (2, 1, 'Insertion Sort', '', 'https://en.wikipedia.org/wiki/Insertion_sort', '{"0":{"n":"a","t":"[i","v":"R","s":9},"4":{"n":"i","t":"i","v":"?","s":2},"5":{"n":"j","t":"i","v":"?","s":2}}', '[{"i":"0","n":"bk"},{"i":"1","n":"as","t":{"k":"v","i":"4"},"f":"0","v":{"k":"c","t":"i","v":"1"}},{"i":"2","n":"cp","l":{"k":"v","i":"4"},"r":{"k":"e","l":{"k":"p","i":"0","p":"length"},"r":{"k":"c","t":"i","v":"1"},"o":"-"},"o":"le"},{"i":"3","n":"bk","c":["2"]},{"i":"4","n":"bk"},{"i":"5","n":"as","t":{"k":"v","i":"5"},"f":"4","v":{"k":"v","i":"4"}},{"i":"6","n":"cp","l":{"k":"v","i":"5"},"r":{"k":"c","t":"i","v":"0"},"o":"gt"},{"i":"7","n":"cp","l":{"k":"i","i":"0","x":{"k":"e","l":{"k":"v","i":"5"},"r":{"k":"c","t":"i","v":"1"},"o":"-"}},"r":{"k":"i","i":"0","x":{"k":"v","i":"5"}},"o":"gt"},{"i":"8","n":"bk","c":["6","7"]},{"i":"9","n":"sw","l":{"k":"i","i":"0","x":{"k":"v","i":"5"}},"r":{"k":"i","i":"0","x":{"k":"e","l":{"k":"v","i":"5"},"r":{"k":"c","t":"i","v":"1"},"o":"-"}}},{"i":"10","n":"ic","v":{"k":"v","i":"5"},"o":"d"},{"i":"11","n":"bk","c":["9","10"]},{"i":"12","n":"wl","c":"8","b":"11","o":"l"},{"i":"13","n":"ic","v":{"k":"v","i":"4"},"o":"i"},{"i":"14","n":"bk","c":["5","12","13"]},{"i":"15","n":"wl","c":"3","b":"14","o":"l"},{"i":"16","n":"bk","c":["1","15"]}]', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
  (3, 1, 'Selection Sort', '', 'https://en.wikipedia.org/wiki/Selection_sort', '{"0":{"n":"a","t":"[i","v":"R","s":9},"1":{"n":"i","t":"i","v":"?","s":2},"2":{"n":"j","t":"i","v":"?","s":2},"3":{"n":"jmin","t":"i","v":"?","s":2}}', '[{"i":"0","n":"bk"},{"i":"1","n":"as","t":{"k":"v","i":"1"},"f":"0","v":{"k":"c","t":"i","v":"0"}},{"i":"2","n":"cp","l":{"k":"v","i":"1"},"r":{"k":"e","l":{"k":"p","i":"0","p":"length"},"r":{"k":"c","t":"i","v":"1"},"o":"-"},"o":"lt"},{"i":"3","n":"bk","c":["2"]},{"i":"4","n":"bk"},{"i":"5","n":"as","t":{"k":"v","i":"3"},"f":"4","v":{"k":"v","i":"1"}},{"i":"6","n":"bk"},{"i":"7","n":"as","t":{"k":"v","i":"2"},"f":"6","v":{"k":"e","l":{"k":"v","i":"1"},"r":{"k":"c","t":"i","v":"1"},"o":"+"}},{"i":"8","n":"cp","l":{"k":"v","i":"2"},"r":{"k":"p","i":"0","p":"length"},"o":"lt"},{"i":"9","n":"bk","c":["8"]},{"i":"10","n":"cp","l":{"k":"i","i":"0","x":{"k":"v","i":"2"}},"r":{"k":"i","i":"0","x":{"k":"v","i":"3"}},"o":"lt"},{"i":"11","n":"bk","c":["10"]},{"i":"12","n":"bk"},{"i":"13","n":"as","t":{"k":"v","i":"3"},"f":"12","v":{"k":"v","i":"2"}},{"i":"14","n":"bk","c":["13"]},{"i":"15","n":"bk"},{"i":"16","n":"if","c":"11","b":"14","e":"15","o":"l"},{"i":"17","n":"ic","v":{"k":"v","i":"2"},"o":"i"},{"i":"18","n":"bk","c":["16","17"]},{"i":"19","n":"wl","c":"9","b":"18","o":"l"},{"i":"20","n":"cp","l":{"k":"v","i":"3"},"r":{"k":"v","i":"1"},"o":"ne"},{"i":"21","n":"bk","c":["20"]},{"i":"22","n":"sw","l":{"k":"i","i":"0","x":{"k":"v","i":"1"}},"r":{"k":"i","i":"0","x":{"k":"v","i":"3"}}},{"i":"23","n":"bk","c":["22"]},{"i":"24","n":"bk"},{"i":"25","n":"if","c":"21","b":"23","e":"24","o":"l"},{"i":"26","n":"ic","v":{"k":"v","i":"1"},"o":"i"},{"i":"27","n":"bk","c":["5","7","19","25","26"]},{"i":"28","n":"wl","c":"3","b":"27","o":"l"},{"i":"29","n":"bk","c":["1","28"]}]', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
  (4, 1, 'Euklidscher Algorithmus (Division)', 'Berechnet den grÃ¶ÃŸten gemeinsamen Teiler (divisionsbasiert)', 'Berechnet den grÃ¶ÃŸten gemeinsamen Teiler (ggT) zweier zufÃ¤lliger Zahlen.\n\nhttps://de.wikipedia.org/wiki/Euklidischer_Algorithmus', '{"0":{"n":"a","t":"i","v":1071,"s":2},"1":{"n":"b","t":"i","v":462,"s":2},"2":{"n":"tmp","t":"i","v":"?","s":2}}', '[{"i":"0","n":"cp","l":{"k":"v","i":"1"},"r":{"k":"c","t":"i","v":"0"},"o":"ne"},{"i":"1","n":"bk","c":["0"]},{"i":"2","n":"bk"},{"i":"3","n":"as","t":{"k":"v","i":"2"},"f":"2","v":{"k":"v","i":"1"}},{"i":"4","n":"bk"},{"i":"5","n":"as","t":{"k":"v","i":"1"},"f":"4","v":{"k":"e","l":{"k":"v","i":"0"},"r":{"k":"v","i":"1"},"o":"%"}},{"i":"6","n":"bk"},{"i":"7","n":"as","t":{"k":"v","i":"0"},"f":"6","v":{"k":"v","i":"2"}},{"i":"8","n":"bk","c":["3","5","7"]},{"i":"9","n":"wl","c":"1","b":"8","o":"l"},{"i":"10","n":"bk"},{"i":"11","n":"rt","v":{"k":"v","i":"0"},"r":"10"},{"i":"12","n":"bk","c":["9","11"]}]', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
  (5, 1, 'Euklidischer Algorithmus (Subtraktion)', 'Berechnet den grÃ¶ÃŸten gemeinsamen Teiler subtraktionsbasiert', 'https://de.wikipedia.org/wiki/Euklidischer_Algorithmus\n\n```\nfunction ggt(a, b)\n  while a â‰  b \n    if a > b\n      a := a âˆ’ b\n    else\n      b := b âˆ’ a\n  return a\n```', '[{"n":"a","t":"i","v":"R","s":2},{"n":"b","t":"i","v":"R","s":2}]', '[{"i":"0","n":"cp","l":{"k":"v","i":"0"},"r":{"k":"v","i":"1"},"o":"ne"},{"i":"1","n":"bk","c":["0"]},{"i":"2","n":"cp","l":{"k":"v","i":"0"},"r":{"k":"v","i":"1"},"o":"gt"},{"i":"3","n":"bk","c":["2"]},{"i":"4","n":"bk"},{"i":"5","n":"as","t":{"k":"v","i":"0"},"f":"4","v":{"k":"e","l":{"k":"v","i":"0"},"r":{"k":"v","i":"1"},"o":"-"}},{"i":"6","n":"bk","c":["5"]},{"i":"7","n":"bk"},{"i":"8","n":"as","t":{"k":"v","i":"1"},"f":"7","v":{"k":"e","l":{"k":"v","i":"1"},"r":{"k":"v","i":"0"},"o":"-"}},{"i":"9","n":"bk","c":["8"]},{"i":"10","n":"if","c":"3","b":"6","e":"9","o":"l"},{"i":"11","n":"bk","c":["10"]},{"i":"12","n":"wl","c":"1","b":"11","o":"l"},{"i":"13","n":"bk"},{"i":"14","n":"rt","v":{"k":"v","i":"0"},"r":"13"},{"i":"15","n":"bk","c":["12","14"]}]', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
  (6, 1, 'Euklidischer Algorithmus (Rekursion)', 'Berechnet den grÃ¶ÃŸten gemeinsamen Teiler (rekursiv)', 'https://de.wikipedia.org/wiki/Euklidischer_Algorithmus\n\n```\nfunction ggt(a, b)\n  if b = 0\n    return a\n  else\n    return ggt(b, a mod b)\n```', '{"0":{"n":"a","t":"i","v":"P","s":2},"1":{"n":"b","t":"i","v":"P","s":2}}', '[{"i":"0","n":"cp","l":{"k":"v","i":"1"},"r":{"k":"c","t":"i","v":"0"},"o":"eq"},{"i":"1","n":"bk","c":["0"]},{"i":"2","n":"bk"},{"i":"3","n":"rt","v":{"k":"v","i":"0"},"r":"2"},{"i":"4","n":"bk","c":["3"]},{"i":"5","n":"vl","v":{"k":"v","i":"1"}},{"i":"6","n":"vl","v":{"k":"e","l":{"k":"v","i":"0"},"r":{"k":"v","i":"1"},"o":"%"}},{"i":"7","n":"bk","c":["5","6"]},{"i":"8","n":"ft","c":"56","p":"7"},{"i":"9","n":"bk","c":["8"]},{"i":"10","n":"rt","v":{"k":"v","i":"1"},"r":"9"},{"i":"11","n":"bk","c":["10"]},{"i":"12","n":"if","c":"1","b":"4","e":"11","o":"l"},{"i":"13","n":"bk","c":["12"]}]', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
  (7, 1, 'Euklidischer Algorithmus Rekursiv', 'Startet den rekursiven Euklidischen Algorithmus', '', '{"0":{"n":"a","t":"i","v":"R","s":2},"1":{"n":"b","t":"i","v":"R","s":2}}', '[{"i":"0","n":"vl","v":{"k":"v","i":"0"}},{"i":"1","n":"vl","v":{"k":"v","i":"1"}},{"i":"2","n":"bk","c":["0","1"]},{"i":"3","n":"ft","c":"56","p":"2"},{"i":"4","n":"bk","c":["3"]},{"i":"5","n":"rt","r":"4"},{"i":"6","n":"bk","c":["5"]}]', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
  (8, 1, 'Minimum (Zufall)', 'Berechnet das Minimum zweier Zahlen', 'Berechnet das __Minimum__ zweier Zufallszahlen.', '{"0":{"n":"a","t":"i","v":"R","s":2},"1":{"n":"b","t":"i","v":"R","s":2},"2":{"n":"min","t":"i","v":"?","s":2}}', '[{"i":"0","n":"cm","c":"Berechnet das Minimum zweier Zahlen."},{"i":"1","n":"bk"},{"i":"2","n":"as","t":{"k":"v","i":"2"},"f":"1","v":{"k":"v","i":"0"}},{"i":"3","n":"cp","l":{"k":"v","i":"1"},"r":{"k":"v","i":"0"},"o":"lt"},{"i":"4","n":"bk","c":["3"]},{"i":"5","n":"bk"},{"i":"6","n":"as","t":{"k":"v","i":"2"},"f":"5","v":{"k":"v","i":"1"}},{"i":"7","n":"bk","c":["6"]},{"i":"8","n":"bk"},{"i":"9","n":"if","c":"4","b":"7","e":"8","o":"l"},{"i":"10","n":"bk"},{"i":"11","n":"rt","v":{"k":"v","i":"2"},"r":"10"},{"i":"12","n":"bk","c":["0","2","9","11"]}]', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
  (9, 1, 'Lineare Suche', 'Finde einen Wert in einer Liste', 'https://de.wikipedia.org/wiki/Lineare_Suche', '{"0":{"n":"a","t":"[i","v":"R","s":9},"1":{"n":"key","t":"i","v":13,"s":2},"3":{"n":"i","t":"i","v":"?","s":2}}', '[{"i":"0","n":"bk"},{"i":"1","n":"as","t":{"k":"v","i":"3"},"f":"0","v":{"k":"c","t":"i","v":"0"}},{"i":"2","n":"cp","l":{"k":"v","i":"3"},"r":{"k":"p","i":"0","p":"length"},"o":"lt"},{"i":"3","n":"bk","c":["2"]},{"i":"4","n":"cp","l":{"k":"v","i":"1"},"r":{"k":"i","i":"0","x":{"k":"v","i":"3"}},"o":"eq"},{"i":"5","n":"bk","c":["4"]},{"i":"6","n":"bk"},{"i":"7","n":"rt","v":{"k":"v","i":"3"},"r":"6"},{"i":"8","n":"bk","c":["7"]},{"i":"9","n":"ic","v":{"k":"v","i":"3"},"o":"i"},{"i":"10","n":"bk","c":["9"]},{"i":"11","n":"if","c":"5","b":"8","e":"10","o":"l"},{"i":"12","n":"bk","c":["11"]},{"i":"13","n":"wl","c":"3","b":"12","o":"l"},{"i":"14","n":"bk"},{"i":"15","n":"rt","v":{"k":"c","t":"b","v":"false"},"r":"14"},{"i":"16","n":"bk","c":["1","13","15"]}]', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
  (10, 1, 'BinÃ¤re Suche', 'Finde einen Wert in einer sortierten Liste', 'https://de.wikipedia.org/wiki/BinÃ¤re_Suche', '{"0":{"n":"a","t":"[i","v":"1,7,12,17,19,25,36,37,40","s":9},"1":{"n":"key","t":"i","v":17,"s":2},"2":{"n":"imin","t":"i","v":"?","s":2},"3":{"n":"imax","t":"i","v":"?","s":2},"4":{"n":"imid","t":"i","v":"?","s":2}}', '[{"i":"0","n":"bk"},{"i":"1","n":"as","t":{"k":"v","i":"2"},"f":"0","v":{"k":"c","t":"i","v":"0"}},{"i":"2","n":"bk"},{"i":"3","n":"as","t":{"k":"v","i":"3"},"f":"2","v":{"k":"p","i":"0","p":"length-1"}},{"i":"4","n":"cp","l":{"k":"v","i":"2"},"r":{"k":"v","i":"3"},"o":"le"},{"i":"5","n":"bk","c":["4"]},{"i":"6","n":"bk"},{"i":"7","n":"as","t":{"k":"v","i":"4"},"f":"6","v":{"k":"e","l":{"k":"e","l":{"k":"v","i":"2"},"r":{"k":"v","i":"3"},"o":"+"},"r":{"k":"c","t":"i","v":"2"},"o":"\\/"}},{"i":"8","n":"cp","l":{"k":"i","i":"0","x":{"k":"v","i":"4"}},"r":{"k":"v","i":"1"},"o":"eq"},{"i":"9","n":"bk","c":["8"]},{"i":"10","n":"bk"},{"i":"11","n":"rt","v":{"k":"v","i":"4"},"r":"10"},{"i":"12","n":"bk","c":["11"]},{"i":"13","n":"cp","l":{"k":"i","i":"0","x":{"k":"v","i":"4"}},"r":{"k":"v","i":"1"},"o":"lt"},{"i":"14","n":"bk","c":["13"]},{"i":"15","n":"bk"},{"i":"16","n":"as","t":{"k":"v","i":"2"},"f":"15","v":{"k":"e","l":{"k":"v","i":"4"},"r":{"k":"c","t":"i","v":"1"},"o":"+"}},{"i":"17","n":"bk","c":["16"]},{"i":"18","n":"bk"},{"i":"19","n":"as","t":{"k":"v","i":"3"},"f":"18","v":{"k":"e","l":{"k":"v","i":"4"},"r":{"k":"c","t":"i","v":"1"},"o":"-"}},{"i":"20","n":"bk","c":["19"]},{"i":"21","n":"if","c":"14","b":"17","e":"20","o":"l"},{"i":"22","n":"bk","c":["21"]},{"i":"23","n":"if","c":"9","b":"12","e":"22","o":"l"},{"i":"24","n":"bk","c":["7","23"]},{"i":"25","n":"wl","c":"5","b":"24","o":"l"},{"i":"26","n":"bk"},{"i":"27","n":"rt","v":{"k":"c","t":"i","v":"-1"},"r":"26"},{"i":"28","n":"bk","c":["1","3","25","27"]}]', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

INSERT INTO `tag` (`tag`, `aid`) VALUES
  ('euklid', 4),
  ('euklid', 5),
  ('euklid', 6),
  ('euklid', 7),
  ('ggt', 4),
  ('ggt', 5),
  ('ggt', 6),
  ('ggt', 7),
  ('minimum', 8),
  ('rekursion', 6),
  ('rekursion', 7),
  ('sort', 1),
  ('sort', 2),
  ('sort', 3),
  ('subtraktion', 5),
  ('suche', 9),
  ('suche', 10),
  ('zufall', 8);