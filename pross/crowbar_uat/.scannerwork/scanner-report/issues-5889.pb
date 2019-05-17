“
lessS2260÷Parse error at line 180 column 38:

170:     .m(@x)                     {case:           1}
171:     .m(@x) when not(default()) {not-default-1: @x}
172:     .m(@x) when not(default()) {not-default-2: @x}
173: 
174:     .m(2);
175: }
176: 
177: // default & scope
178: 
179: guard-default-scopes {
180:     .s1() {.m(@v)                  {1: no condition}}
                                          ^
181:     .s2() {.m(@v) when (@v)        {2: when true}}
182:     .s3() {.m(@v) when (default()) {3: when default}}
183: 
184:     &-3 {
185:         .s2();
186:         .s3();
187:         .m(false);
188:     }
189: 
190:     &-1 { 2´´ 5