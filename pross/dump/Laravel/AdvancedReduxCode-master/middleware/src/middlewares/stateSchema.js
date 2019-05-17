export default {
  $id: 'http://example.com/example.json',
  type: 'object',
  definitions: {},
  $schema: 'http://json-schema.org/draft-07/schema#',
  properties: {
    comments: {
      $id: '/properties/comments',
      type: 'array',
      items: {
        $id: '/properties/comments/items',
        type: 'string',
        title: 'The 0th Schema ',
        default: '',
        examples: ['Comment #1', 'Comment #2']
      }
    },
    auth: {
      $id: '/properties/auth',
      type: 'boolean',
      title: 'The Auth Schema ',
      default: false,
      examples: [true]
    }
  }
};
