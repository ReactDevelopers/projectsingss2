¤
lessS2260ŠParse error at line 6 column 6:

 1: .named-colors-in-expressions {
 2:     color-0: 0 -red;  
 3:     color-1: 1 - red; 
 4:     color-2: red * 2;
 5:     color-3: 2 * red;
 6:     @3: -red;
         ^
 7:     &-bar@{3} {x: y}
 8:     @color: red;
 9:     &-bar@{color} {a: a};
10:     background-color: blue-2;
11:     color: green-black;
12:     animation: blue-change 5s infinite;
13: }
14: 2 