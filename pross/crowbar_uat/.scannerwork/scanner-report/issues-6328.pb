õ
lessS2260ÅParse error at line 8 column 28:

 1: Ôªø.eval {
 2:     js: `42`;
 3:     js: `1 + 1`;
 4:     js: `"hello world"`;
 5:     js: `[1, 2, 3]`;
 6:     title: `typeof process.title`;
 7:     ternary: `(1 + 1 == 2 ? true : false)`;
 8:     multiline: `(function(){var x = 1 + 1;
                               ^
 9:            return x})()`;
10: }
11: .scope {
12:     @foo: 42;
13:     var: `parseInt(this.foo.toJS())`;
14:     escaped: ~`2 + 5 + 'px'`;
15: }
16: .vars {
17:     @var: `4 + 4`;
18:     width: @var; 2 *