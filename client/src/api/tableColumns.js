export let lesson_columns = [
  { label: '学号', field: 'id', search: true },
  { label: '中文姓名', field: 'cn_name' },
  { label: '英文姓名', field: 'en_name' },
  { label: '班级', field: 'class' }
];

export let lesson_name_columns = [
  { label: '学号', field: 'id', search: true },
  { label: '中文姓名', field: 'cn_name' },
  { label: '英文姓名', field: 'en_name' },
  { label: '班级', field: 'class' },
  { label: '活动名称', field: 'lesson_name' },
];

export let student_columns = [
  { label: '班号', field: 'class_no' },
  { label: '学号', field: 'id', search: true },
  { label: '中文姓名', field: 'cn_name' },
  { label: '英文姓名', field: 'en_name' },
  { label: '性别', field: 'gender' },
  { label: '1-3', field: 'first' },
  { label: '4-5', field: 'second' },
  { label: '6-7', field: 'third' }
];

export let student_class_columns = [
  { label: '班号', field: 'class_no' },
  { label: '学号', field: 'id', search: true },
  { label: '中文姓名', field: 'cn_name' },
  { label: '英文姓名', field: 'en_name' },
  { label: '性别', field: 'gender' },
  { label: '班级', field: 'class' },
  { label: '1-3', field: 'first' },
  { label: '4-5', field: 'second' },
  { label: '6-7', field: 'third' }
];

export let lesson_list_columns = [
  { label: '活动名称', field: 'name', search: true },
  { label: '科目/部门', field: 'subject' },
  { label: '操作', field: 'action' }
]