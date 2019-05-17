‹
lessS2260ñParse error at line 37 column 38:

27: .mixin(@arg) {
28:     @local: local;
29:     @{global}-@{local}-@{arg}-property: strong;
30: }
31: 
32: .merge(@p, @v) {
33:     &-merge {
34:         @prefix: pre;
35:         @suffix: ish;
36:         @{prefix}-property-ish+       :high;
37:         pre-property-@{suffix}    +: middle;
                                         ^
38:         @{prefix}-property-@{suffix}+:  low;
39:         @{prefix}-property-@{p}   +  :   @v;
40: 
41:         @subterfuge: ~'+';
42:         pre-property-ish@{subterfuge}: nice try dude;
43:     }
44: }
45: 
46: pi-indirect-vars {
47:     @{p}: @p; 2%% ,