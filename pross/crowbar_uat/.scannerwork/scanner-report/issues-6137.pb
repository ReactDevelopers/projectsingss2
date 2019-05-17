ø
lessS2260ÞParse error at line 113 column 7:

103:     extract-1: extract(@v, 2);
104:     length-2:  length(extract(@v, 2));
105:     extract-2: extract(extract(@v, 2), 2);
106: 
107:     &-as-args {.mixin-args(@a @b @c)}
108: }
109: 
110: .md-3D {
111:     @a: a b c d, 1 2 3 4;
112:     @b: 5 6 7 8, e f g h;
113:     .3D(@a, @b);
           ^
114: 
115:     .3D(...) {
116: 
117:         @v1:       @arguments;
118:         length-1:  length(@v1);
119:         extract-1: extract(@v1, 1);
120: 
121:         @v2:       extract(@v1, 2);
122:         length-2:  length(@v2);
123:         extract-2: extract(@v2, 1); 2qq 