<?php
/*
 * Limb PHP Framework
 *
 * @link http://limb-project.com
 * @copyright  Copyright &copy; 2004-2007 BIT(http://bit-creative.com)
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */
 
class lmbARQueryTest extends lmbARBaseTestCase
{
  protected $tables_to_cleanup = array('test_one_table_object', 
                                       'person_for_test', 
                                       'social_security_for_test',
                                       'lecture_for_test',
                                       'course_for_test');
  
  function testSimpleFetch()
  {
    $object1 = $this->creator->createOneTableObject();
    $object2 = $this->creator->createOneTableObject();
    
    $query = new lmbARQuery('TestOneTableObject', $this->conn);
    $iterator = $query->fetch();
    $arr = $iterator->getArray();
    
    $this->assertIsA($arr[0], 'TestOneTableObject');
    $this->assertEqual($arr[0]->getAnnotation(), $object1->getAnnotation());
    $this->assertIsA($arr[1], 'TestOneTableObject');
    $this->assertEqual($arr[1]->getAnnotation(), $object2->getAnnotation());
  }
  
  function testFetch_Join_RelatedHasOneObject()
  {
    $person1 = $this->creator->createPerson();
    $person2 = $this->creator->createPerson();
    
    $this->conn->resetQueries();
    
    $query = new lmbARQuery('PersonForTest', $this->conn);
    $query->join('social_security');
    $iterator = $query->fetch();
    $arr = $iterator->getArray();
    
    $this->assertEqual($this->conn->countQueries(), 1);

    //make sure we really eager fetching
    $this->db->delete('social_security_for_test');

    $this->conn->resetQueries();
    
    $this->assertIsA($arr[0], 'PersonForTest');
    $this->assertEqual($arr[0]->getName(), $person1->getName());
    $this->assertIsA($arr[0]->getSocialSecurity(), 'SocialSecurityForTest');
    $this->assertEqual($arr[0]->getSocialSecurity()->getCode(), $person1->getSocialSecurity()->getCode());
    
    $this->assertIsA($arr[1], 'PersonForTest');
    $this->assertEqual($arr[1]->getName(), $person2->getName());
    $this->assertIsA($arr[1]->getSocialSecurity(), 'SocialSecurityForTest');
    $this->assertEqual($arr[1]->getSocialSecurity()->getCode(), $person2->getSocialSecurity()->getCode());

    $this->assertEqual($this->conn->countQueries(), 0);
  }

  function testFetch_Join_RelatedBelongsToObject()
  {
    $person1 = $this->creator->createPerson();
    $ss1 = $person1->getSocialSecurity();
    $person2 = $this->creator->createPerson();
    $ss2 = $person2->getSocialSecurity();
    
    $this->conn->resetQueries();
    
    $query = new lmbARQuery('SocialSecurityForTest', $this->conn);
    $query->join('person');
    $iterator = $query->fetch();
    $arr = $iterator->getArray();

    $this->assertEqual($this->conn->countQueries(), 1);
    
    //make sure we really eager fetching
    $this->db->delete('person_for_test');

    $this->conn->resetQueries();
    
    $this->assertIsA($arr[0], 'SocialSecurityForTest');
    $this->assertEqual($arr[0]->getCode(), $ss1->getCode());
    $this->assertIsA($arr[0]->getPerson(), 'PersonForTest');
    $this->assertEqual($arr[0]->getPerson()->getName(), $person1->getName());
    
    $this->assertIsA($arr[1], 'SocialSecurityForTest');
    $this->assertEqual($arr[1]->getCode(), $ss2->getCode());
    $this->assertIsA($arr[1]->getPerson(), 'PersonForTest');
    $this->assertEqual($arr[1]->getPerson()->getName(), $person2->getName());

    $this->assertEqual($this->conn->countQueries(), 0);
  }

  function testFetch_Join_RelatedManyBelongsToObject()
  {
    $course1 = $this->creator->createCourse();
    $course2 = $this->creator->createCourse();
    $lecture1 = $this->creator->createLecture($course1);
    $lecture2 = $this->creator->createLecture($course1);
    $lecture3 = $this->creator->createLecture($course2);
    
    $this->conn->resetQueries();
    
    $query = new lmbARQuery('LectureForTest', $this->conn);
    $query->join('course');
    $iterator = $query->fetch();
    $arr = $iterator->getArray();
    
    $this->assertEqual($this->conn->countQueries(), 1);

    //make sure we really eager fetching
    $this->db->delete('course_for_test');
    
    $this->conn->resetQueries();
    
    $this->assertIsA($arr[0], 'LectureForTest');
    $this->assertEqual($arr[0]->getTitle(), $lecture1->getTitle());
    $this->assertIsA($arr[0]->getCourse(), 'CourseForTest');
    $this->assertEqual($arr[0]->getCourse()->getTitle(), $course1->getTitle());
    
    $this->assertIsA($arr[1], 'LectureForTest');
    $this->assertEqual($arr[1]->getTitle(), $lecture2->getTitle());
    $this->assertIsA($arr[1]->getCourse(), 'CourseForTest');
    $this->assertEqual($arr[1]->getCourse()->getTitle(), $course1->getTitle());
    
    $this->assertIsA($arr[2], 'LectureForTest');
    $this->assertEqual($arr[2]->getTitle(), $lecture3->getTitle());
    $this->assertIsA($arr[2]->getCourse(), 'CourseForTest');
    $this->assertEqual($arr[2]->getCourse()->getTitle(), $course2->getTitle());

    $this->assertEqual($this->conn->countQueries(), 0);
  }

  function testFetch_Attach_RelatedHasOneObjects()
  {
    $person1 = $this->creator->createPerson();
    $person2 = $this->creator->createPerson();
    
    $this->conn->resetQueries();
    
    $query = new lmbARQuery('PersonForTest', $this->conn);
    // note attach() has the same effect as join() but workds is a different way - it produces another sql request 
    $iterator = $query->attach('social_security')->fetch();
    $arr = $iterator->getArray();
    
    $this->assertEqual($this->conn->countQueries(), 2);

    //make sure we really eager fetching
    $this->db->delete('social_security_for_test');
    
    $this->conn->resetQueries();
    
    $this->assertIsA($arr[0], 'PersonForTest');
    $this->assertEqual($arr[0]->getName(), $person1->getName());
    $this->assertIsA($arr[0]->getSocialSecurity(), 'SocialSecurityForTest');
    $this->assertEqual($arr[0]->getSocialSecurity()->getCode(), $person1->getSocialSecurity()->getCode());
    
    $this->assertIsA($arr[1], 'PersonForTest');
    $this->assertEqual($arr[1]->getName(), $person2->getName());
    $this->assertIsA($arr[1]->getSocialSecurity(), 'SocialSecurityForTest');
    $this->assertEqual($arr[1]->getSocialSecurity()->getCode(), $person2->getSocialSecurity()->getCode());

    $this->assertEqual($this->conn->countQueries(), 0);
  }

  function testFetch_Attach_RelatedBelongsToObjects()
  {
    $id = $this->db->insert('person_for_test', array('id' => 100, 'name' => 'junky person'));
    
    $person1 = $this->creator->createPerson();
    $person2 = $this->creator->createPerson();
    
    $this->db->delete('person_for_test', 'id = ' . $id);
    
    $this->conn->resetQueries();
    
    $query = new lmbARQuery('SocialSecurityForTest', $this->conn);
    // note attach() has the same effect as join() but workds is a different way - it produces another sql request 
    $arr = $query->attach('person')->fetch()->getArray();

    $this->assertEqual($this->conn->countQueries(), 2);
    
    //make sure we really eager fetching
    $this->db->delete('person_for_test');

    $this->conn->resetQueries();
    
    $this->assertIsA($arr[0], 'SocialSecurityForTest');
    $this->assertEqual($arr[0]->getCode(), $person1->getSocialSecurity()->getCode());
    $this->assertIsA($arr[0]->getPerson(), 'PersonForTest');
    $this->assertEqual($arr[0]->getPerson()->getName(), $person1->getName());
    
    $this->assertIsA($arr[1], 'SocialSecurityForTest');
    $this->assertEqual($arr[1]->getCode(), $person2->getSocialSecurity()->getCode());
    $this->assertIsA($arr[1]->getPerson(), 'PersonForTest');
    $this->assertEqual($arr[1]->getPerson()->getName(), $person2->getName());

    $this->assertEqual($this->conn->countQueries(), 0);
  }

  function testFetch_Attach_RelatedManyBelongsToObjects()
  {
   $course = $this->creator->createCourse();
    
    $alt_course1 = $this->creator->createCourse();
    $alt_course2 = $this->creator->createCourse();
    
    $lecture1 = $this->creator->createLecture($course, $alt_course1);
    $lecture2 = $this->creator->createLecture($course, $alt_course2);
    $lecture3 = $this->creator->createLecture($course, $alt_course1);
    
    $this->conn->resetQueries();
    
    $query = new lmbARQuery('LectureForTest', $this->conn);
    $arr = $query->attach('course')->attach('alt_course')->fetch()->getArray();
    
    $this->assertEqual($this->conn->countQueries(), 3);
    
    //make sure we really eager fetching
    $this->db->delete('course_for_test');

    $this->conn->resetQueries();
    
    $this->assertIsA($arr[0], 'LectureForTest');
    $this->assertEqual($arr[0]->getTitle(), $lecture1->getTitle());
    $this->assertIsA($arr[0]->getCourse(), 'CourseForTest');
    $this->assertEqual($arr[0]->getCourse()->getTitle(), $course->getTitle());
    $this->assertIsA($arr[0]->getAltCourse(), 'CourseForTest');
    $this->assertEqual($arr[0]->getAltCourse()->getTitle(), $alt_course1->getTitle());

    $this->assertIsA($arr[1], 'LectureForTest');
    $this->assertEqual($arr[1]->getTitle(), $lecture2->getTitle());
    $this->assertIsA($arr[1]->getCourse(), 'CourseForTest');
    $this->assertEqual($arr[1]->getCourse()->getTitle(), $course->getTitle());
    $this->assertIsA($arr[1]->getAltCourse(), 'CourseForTest');
    $this->assertEqual($arr[1]->getAltCourse()->getTitle(), $alt_course2->getTitle());

    $this->assertIsA($arr[2], 'LectureForTest');
    $this->assertEqual($arr[2]->getTitle(), $lecture3->getTitle());
    $this->assertIsA($arr[2]->getCourse(), 'CourseForTest');
    $this->assertEqual($arr[2]->getCourse()->getTitle(), $course->getTitle());
    $this->assertIsA($arr[2]->getAltCourse(), 'CourseForTest');
    $this->assertEqual($arr[2]->getAltCourse()->getTitle(), $alt_course1->getTitle()); 
    
    $this->assertEqual($this->conn->countQueries(), 0);
  }

  function testFetch_Attach_RelatedHasMany()
  {
    $course1 = $this->creator->createCourse();
    $course2 = $this->creator->createCourse();
    
    $lecture1 = $this->creator->createLecture($course1, null, 'ZZZ');
    $lecture2 = $this->creator->createLecture($course2, null, 'CCC');
    $lecture3 = $this->creator->createLecture($course1, null, 'AAA');
    $lecture4 = $this->creator->createLecture($course1, null, 'BBB');
    
    $this->conn->resetQueries();
    
    $query = new lmbARQuery('CourseForTest', $this->conn);
    $arr = $query->attach('lectures', array('sort' => array('title' => 'ASC')))->fetch()->getArray();
    
    $this->assertEqual($this->conn->countQueries(), 2);
    
    //make sure we really eager fetching
    $this->db->delete('lecture_for_test');
    
    $this->conn->resetQueries();
    
    $this->assertIsA($arr[0], 'CourseForTest');
    $this->assertEqual($arr[0]->getTitle(), $course1->getTitle());
    $lectures = $arr[0]->getLectures();
    $this->assertEqual(count($lectures), 3);
    $this->assertEqual($lectures[0]->getId(), $lecture3->getId());
    $this->assertEqual($lectures[0]->getTitle(), 'AAA');
    $this->assertEqual($lectures[1]->getId(), $lecture4->getId());
    $this->assertEqual($lectures[1]->getTitle(), 'BBB');
    $this->assertEqual($lectures[2]->getId(), $lecture1->getId());
    $this->assertEqual($lectures[2]->getTitle(), 'ZZZ');
    
    $this->assertIsA($arr[1], 'CourseForTest');
    $this->assertEqual($arr[1]->getTitle(), $course2->getTitle());
    $lectures = $arr[1]->getLectures();
    $this->assertEqual(count($lectures), 1);
    $this->assertEqual($lectures[0]->getId(), $lecture2->getId());
    $this->assertEqual($lectures[0]->getTitle(), 'CCC');
    
    $this->assertEqual($this->conn->countQueries(), 0);
  }

  function testFetch_Attach_RelatedHasManyToMany()
  {
    $user1 = $this->creator->createUser();
    $user2 = $this->creator->createUser();

    $group1 = $this->creator->createGroup('AAA');
    $group2 = $this->creator->createGroup('BBB');
    $group3 = $this->creator->createGroup('ZZZ');
    
    $group1->setUsers(array($user1, $user2));
    $group2->setUsers(array($user2));
    $group3->setUsers(array($user1));
     
    $this->conn->resetQueries();
    
    $query = new lmbARQuery('UserForTest', $this->conn);
    $arr = $query->attach('groups', array('sort' => array('title' => 'DESC')))->fetch()->getArray();
    
    $this->assertEqual($this->conn->countQueries(), 2);
    
    //make sure we really eager fetching
    $this->db->delete('group_for_test');
    $this->db->delete('user2group_for_test');

    $this->conn->resetQueries();
    
    $this->assertIsA($arr[0], 'UserForTest');
    $this->assertEqual($arr[0]->getFirstName(), $user1->getFirstName());
    $groups = $arr[0]->getGroups();
    $this->assertEqual(count($groups), 2);
    $this->assertEqual($groups[0]->getId(), $group3->getId());
    $this->assertEqual($groups[0]->getTitle(), 'ZZZ');
    $this->assertEqual($groups[1]->getId(), $group1->getId());
    $this->assertEqual($groups[1]->getTitle(), 'AAA');
    
    $this->assertIsA($arr[1], 'UserForTest');
    $this->assertEqual($arr[1]->getFirstName(), $user2->getFirstName());
    $groups = $arr[1]->getGroups();
    $this->assertEqual(count($groups), 2);
    $this->assertEqual($groups[0]->getId(), $group2->getId());
    $this->assertEqual($groups[0]->getTitle(), 'BBB');
    $this->assertEqual($groups[1]->getId(), $group1->getId());
    $this->assertEqual($groups[1]->getTitle(), 'AAA');
    
    $this->assertEqual($this->conn->countQueries(), 0);
  }

  function testFetch_NestedJoinProperty_In_Attach_ForHasMany()
  {
    $course1 = $this->creator->createCourse();
    $course2 = $this->creator->createCourse();
    
    $alt_course1 = $this->creator->createCourse();
    $alt_course2 = $this->creator->createCourse();
    
    $lecture1 = $this->creator->createLecture($course1, $alt_course2);
    $lecture2 = $this->creator->createLecture($course2, $alt_course1);
    $lecture3 = $this->creator->createLecture($course1, $alt_course2);
    $lecture4 = $this->creator->createLecture($course1, $alt_course1);
    
    $this->conn->resetQueries();
    
    $query = new lmbARQuery('CourseForTest', $this->conn);
    $query->where(lmbSQLCriteria :: in('id', array($course1->getId(), $course2->getId())));
    $arr = $query->attach('lectures', array('join' => 'alt_course'))->fetch()->getArray();
    
    $this->assertEqual($this->conn->countQueries(), 2);
    
    //make sure we really eager fetching
    $this->db->delete('lecture_for_test');
    $this->db->delete('course_for_test');

    $this->conn->resetQueries();
    
    $this->assertIsA($arr[0], 'CourseForTest');
    $this->assertEqual($arr[0]->getTitle(), $course1->getTitle());
    $lectures = $arr[0]->getLectures();
    $this->assertEqual(count($lectures), 3);
    $this->assertEqual($lectures[0]->getId(), $lecture1->getId());
    $this->assertEqual($lectures[0]->getAltCourse()->getTitle(), $alt_course2->getTitle());
    $this->assertEqual($lectures[1]->getId(), $lecture3->getId());
    $this->assertEqual($lectures[1]->getAltCourse()->getTitle(), $alt_course2->getTitle());
    $this->assertEqual($lectures[2]->getId(), $lecture4->getId());
    $this->assertEqual($lectures[2]->getAltCourse()->getTitle(), $alt_course1->getTitle());
    
    
    $this->assertIsA($arr[1], 'CourseForTest');
    $this->assertEqual($arr[1]->getTitle(), $course2->getTitle());
    $lectures = $arr[1]->getLectures();
    $this->assertEqual(count($lectures), 1);
    $this->assertEqual($lectures[0]->getId(), $lecture2->getId());
    $this->assertEqual($lectures[0]->getAltCourse()->getTitle(), $alt_course1->getTitle());
    
    $this->assertEqual($this->conn->countQueries(), 0);
  }  
  
  function testFetch_NestedAttachProperty_In_Join()
  {
    $course1 = $this->creator->createCourse();
    $course2 = $this->creator->createCourse();
    
    $alt_course1 = $this->creator->createCourse();
    $alt_course2 = $this->creator->createCourse();
    
    $lecture1 = $this->creator->createLecture($course1, $alt_course2);
    $lecture2 = $this->creator->createLecture($course2, $alt_course1);
    $lecture3 = $this->creator->createLecture($course1, $alt_course2);
    $lecture4 = $this->creator->createLecture($course1, $alt_course1);

    $lecture5 = $this->creator->createLecture($alt_course2);
    $lecture6 = $this->creator->createLecture($alt_course1);
    $lecture7 = $this->creator->createLecture($alt_course2);
    $lecture8 = $this->creator->createLecture($alt_course1);
    
    $this->conn->resetQueries();

    $query = new lmbARQuery('LectureForTest', $this->conn);
    $query->where(lmbSQLCriteria :: equal('course_id', $course1->getId()));
    $iterator = $query->join('alt_course', array('attach' => 'lectures'))->fetch();
    $arr = $iterator->getArray();
    
    $this->assertEqual($this->conn->countQueries(), 2);
    
    //make sure we really eager fetching
    $this->db->delete('lecture_for_test');
    $this->db->delete('course_for_test');
    
    $this->conn->resetQueries();
    
    $this->assertIsA($arr[0], 'LectureForTest');
    $this->assertEqual($arr[0]->getTitle(), $lecture1->getTitle());
    
    $this->assertEqual($arr[0]->getAltCourse()->getTitle(), $alt_course2->getTitle());
    $alt_course_lectures = $arr[0]->getAltCourse()->getLectures();
    $this->assertEqual($alt_course_lectures[0]->getId(), $lecture5->getId());
    $this->assertEqual($alt_course_lectures[1]->getId(), $lecture7->getId());
    
    $this->assertEqual($arr[1]->getId(), $lecture3->getId());
    $this->assertEqual($arr[1]->getAltCourse()->getTitle(), $alt_course2->getTitle());
    $alt_course_lectures = $arr[1]->getAltCourse()->getLectures();
    $this->assertEqual($alt_course_lectures[0]->getId(), $lecture5->getId());
    $this->assertEqual($alt_course_lectures[1]->getId(), $lecture7->getId());
    
    $this->assertEqual($arr[2]->getId(), $lecture4->getId());
    $this->assertEqual($arr[2]->getAltCourse()->getTitle(), $alt_course1->getTitle());
    $alt_course_lectures = $arr[2]->getAltCourse()->getLectures();
    $this->assertEqual($alt_course_lectures[0]->getId(), $lecture6->getId());
    $this->assertEqual($alt_course_lectures[1]->getId(), $lecture8->getId());
    
    $this->assertEqual($this->conn->countQueries(), 0);
  }  

  function testFetchNested_AttachProperty_In_JoinProperty_In_Attach()
  {
    $course1 = $this->creator->createCourse();
    $course2 = $this->creator->createCourse();
    
    $alt_course1 = $this->creator->createCourse();
    $alt_course2 = $this->creator->createCourse();
    
    $lecture1 = $this->creator->createLecture($course1, $alt_course2);
    $lecture2 = $this->creator->createLecture($course2, $alt_course1);
    $lecture3 = $this->creator->createLecture($course1, $alt_course2);
    $lecture4 = $this->creator->createLecture($course1, $alt_course1);
    
    $lecture5 = $this->creator->createLecture($alt_course2);
    $lecture6 = $this->creator->createLecture($alt_course1);
    $lecture7 = $this->creator->createLecture($alt_course2);
    $lecture8 = $this->creator->createLecture($alt_course1);
    
    $this->conn->resetQueries();
    
    $query = new lmbARQuery('CourseForTest', $this->conn);
    $query->where(lmbSQLCriteria :: in('id', array($course1->getId(), $course2->getId())));
    $arr = $query->attach('lectures', array('join' => array('alt_course' => array('attach' => 'lectures'))))->fetch()->getArray();

    $this->assertEqual($this->conn->countQueries(), 3);
    
    //make sure we really eager fetching
    $this->db->delete('lecture_for_test');
    $this->db->delete('course_for_test');

    $this->conn->resetQueries();
    
    $this->assertIsA($arr[0], 'CourseForTest');
    $this->assertEqual($arr[0]->getTitle(), $course1->getTitle());
    $lectures = $arr[0]->getLectures();
    $this->assertEqual(count($lectures), 3);
    $this->assertEqual($lectures[0]->getId(), $lecture1->getId());
    $this->assertEqual($lectures[0]->getAltCourse()->getTitle(), $alt_course2->getTitle());
    $alt_course_lectures = $lectures[0]->getAltCourse()->getLectures();
    $this->assertEqual($alt_course_lectures[0]->getId(), $lecture5->getId());
    $this->assertEqual($alt_course_lectures[1]->getId(), $lecture7->getId());
    
    $this->assertEqual($lectures[1]->getId(), $lecture3->getId());
    $this->assertEqual($lectures[1]->getAltCourse()->getTitle(), $alt_course2->getTitle());
    $alt_course_lectures = $lectures[1]->getAltCourse()->getLectures();
    $this->assertEqual($alt_course_lectures[0]->getId(), $lecture5->getId());
    $this->assertEqual($alt_course_lectures[1]->getId(), $lecture7->getId());
    
    $this->assertEqual($lectures[2]->getId(), $lecture4->getId());
    $this->assertEqual($lectures[2]->getAltCourse()->getTitle(), $alt_course1->getTitle());
    $alt_course_lectures = $lectures[2]->getAltCourse()->getLectures();
    $this->assertEqual($alt_course_lectures[0]->getId(), $lecture6->getId());
    $this->assertEqual($alt_course_lectures[1]->getId(), $lecture8->getId());

    $this->assertIsA($arr[1], 'CourseForTest');
    $this->assertEqual($arr[1]->getTitle(), $course2->getTitle());
    $lectures = $arr[1]->getLectures();
    $this->assertEqual(count($lectures), 1);
    $this->assertEqual($lectures[0]->getId(), $lecture2->getId());
    $this->assertEqual($lectures[0]->getAltCourse()->getTitle(), $alt_course1->getTitle());
    
    $alt_course_lectures = $lectures[0]->getAltCourse()->getLectures();
    $this->assertEqual($alt_course_lectures[0]->getId(), $lecture6->getId());
    $this->assertEqual($alt_course_lectures[1]->getId(), $lecture8->getId());
    
    $this->assertEqual($this->conn->countQueries(), 0);
  }  
  
  function testFetch_NestedJoinProperty_In_Join()
  {
    $program1 = $this->creator->createProgram();
    $program2 = $this->creator->createProgram();
    $course1 = $this->creator->createCourse($program1);
    $course2 = $this->creator->createCourse($program2);
    $lecture1 = $this->creator->createLecture($course1);
    $lecture2 = $this->creator->createLecture($course2);
    
    $this->conn->resetQueries();
    
    $query = new lmbARQuery('LectureForTest', $this->conn);
    $iterator = $query->join('course', array('join' => 'program'))->fetch();
    $arr = $iterator->getArray();
    
    $this->assertEqual($this->conn->countQueries(), 1);
    
    //make sure we really eager fetching
    $this->db->delete('lecture_for_test');
    $this->db->delete('course_for_test');
    $this->db->delete('program_for_test');

    $this->conn->resetQueries();
    
    $this->assertIsA($arr[0], 'LectureForTest');
    $this->assertEqual($arr[0]->getTitle(), $lecture1->getTitle());
    $this->assertEqual($arr[1]->getTitle(), $lecture2->getTitle());
    
    $this->assertEqual($arr[0]->getCourse()->getTitle(), $course1->getTitle());
    $this->assertEqual($arr[1]->getCourse()->getTitle(), $course2->getTitle());

    $this->assertEqual($arr[0]->getCourse()->getProgram()->getTitle(), $program1->getTitle());
    $this->assertEqual($arr[1]->getCourse()->getProgram()->getTitle(), $program2->getTitle());
    
    $this->assertEqual($this->conn->countQueries(), 0);
  }

  function testFetch_NestedAttachProperty_In_Attach()
  {
    $program1 = $this->creator->createProgram();
    $program2 = $this->creator->createProgram();
    $course1 = $this->creator->createCourse($program1);
    $course2 = $this->creator->createCourse($program2);
    $course3 = $this->creator->createCourse($program1);
    $course4 = $this->creator->createCourse($program2);
    $lecture1 = $this->creator->createLecture($course1);
    $lecture2 = $this->creator->createLecture($course2);
    $lecture3 = $this->creator->createLecture($course3);
    $lecture4 = $this->creator->createLecture($course4);
    $lecture5 = $this->creator->createLecture($course1);
    $lecture6 = $this->creator->createLecture($course2);
    $lecture7 = $this->creator->createLecture($course3);
    $lecture8 = $this->creator->createLecture($course4);
    
    $this->conn->resetQueries();
    
    $query = new lmbARQuery('ProgramForTest', $this->conn);
    $iterator = $query->attach('courses', array('attach' => 'lectures'))->fetch();
    
    $arr = $iterator->getArray();

    $this->assertEqual($this->conn->countQueries(), 3);
    
    //make sure we really eager fetching
    $this->db->delete('lecture_for_test');
    $this->db->delete('course_for_test');
    $this->db->delete('program_for_test');

    $this->conn->resetQueries();
    
    $this->assertIsA($arr[0], 'ProgramForTest');
    $this->assertEqual($arr[0]->getTitle(), $program1->getTitle());
    
    $courses = $arr[0]->getCourses();
    
    $this->assertEqual($courses[0]->getTitle(), $course1->getTitle());
    $lectures = $courses[0]->getLectures();
    $this->assertEqual($lectures[0]->getTitle(), $lecture1->getTitle());
    $this->assertEqual($lectures[1]->getTitle(), $lecture5->getTitle());

    $this->assertEqual($courses[1]->getTitle(), $course3->getTitle());
    $lectures = $courses[1]->getLectures();
    $this->assertEqual($lectures[0]->getTitle(), $lecture3->getTitle());
    $this->assertEqual($lectures[1]->getTitle(), $lecture7->getTitle());

    $this->assertEqual($arr[1]->getTitle(), $program2->getTitle());
    
    $courses = $arr[1]->getCourses();
    
    $this->assertEqual($courses[0]->getTitle(), $course2->getTitle());
    $lectures = $courses[0]->getLectures();
    $this->assertEqual($lectures[0]->getTitle(), $lecture2->getTitle());
    $this->assertEqual($lectures[1]->getTitle(), $lecture6->getTitle());

    $this->assertEqual($courses[1]->getTitle(), $course4->getTitle());
    $lectures = $courses[1]->getLectures();
    $this->assertEqual($lectures[0]->getTitle(), $lecture4->getTitle());
    $this->assertEqual($lectures[1]->getTitle(), $lecture8->getTitle());
    
    $this->assertEqual($this->conn->countQueries(), 0);
  }    
}

