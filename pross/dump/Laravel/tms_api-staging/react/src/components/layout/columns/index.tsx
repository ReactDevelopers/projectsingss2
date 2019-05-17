import * as placement from './placement';
import * as course from './course';
import * as courserun from './courserun';
import * as user from './user';

const columns = {
    ...placement,
    ...course,
    ...courserun,
    ...user
}

export default columns;