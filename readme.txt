let startTime = Date.now();

// When submitting
let duration = Math.floor((Date.now() - startTime) / 1000); // duration in seconds

fetch('submit_exam.php', {
    method: 'POST',
    body: JSON.stringify({
        student_id: 'ABC123',
        access_code: 'XYZ789',
        answers: {...},
        duration: duration
    }),
    headers: { 'Content-Type': 'application/json' }
});


https://chatgpt.com/c/6844d516-b924-800e-b9c6-cd6b6a8f94c0



<button class="btn btn-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight">
                        Open Panel
                      </button>
                      
                      <!-- Offcanvas Panel -->
                      <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
                        <div class="offcanvas-header">
                          <h5 id="offcanvasRightLabel">Slide In Panel</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
                        </div>
                        <div class="offcanvas-body">
                          Content goes here...
                        </div>
                      </div>


                      <!-- <div class="dropdown">
                        <button class="bg-primary border text-white p-3 ps-3 pe-5 dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                          class
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                          <li><a class="dropdown-item" href="#">Action</a></li>
                          <li><a class="dropdown-item" href="#">Another action</a></li>
                          <li><a class="dropdown-item" href="#">Something else here</a></li>
                        </ul>
                    </div> -->