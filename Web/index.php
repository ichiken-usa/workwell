<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

    <!-- Self-made CSS -->
    <link href="/css/style.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href='//use.fontawesome.com/releases/v5.11.0/css/all.css' rel='stylesheet' type='text/css' />

    <title>Test | Login</title>
</head>

<body class="text-center bg-light">

    <h1 class="mb-4">Test</h1>

    <form class="border rounded bg-white form-time-table" action="index.php">
        <h2 class="h3 my-3">List</h2>

        <select class="form-select rounded-pill mb-3" aria-label="Default select example">
            <option>2021/11</option>
        </select>

        <table class="table table-hover table-bordered">
            <thead>
                <tr class="bg-light">
                    <th scope="col">Date</th>
                    <th scope="col">Start</th>
                    <th scope="col">End</th>
                    <th scope="col">Break</th>
                    <th scope="col">Comment</th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th scope="row">1(Mon)</th>
                    <td>08:00</td>
                    <td>17:00</td>
                    <td>01:00</td>
                    <td>Test Test Test Test</td>
                    <td><i class="far fa-edit"></i></td>
                </tr>

            </tbody>
        </table>
    </form>

    <!-- Button trigger modal -->
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
        Launch demo modal
    </button>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <p></p>
                    <h5 class="modal-title" id="exampleModalLabel">Register</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-primary" role="alert">
                        11/1(Mon)
                    </div>
                    <div class="container">
                        <div class="row">
                            <div class="col-sm">
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Start">
                                    <span class="input-group-text" id="basic-addon1">Set</span>
                                </div>
                            </div>
                            <div class="col-sm">
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="End">
                                    <span class="input-group-text" id="basic-addon1">Set</span>
                                </div>
                            </div>
                            <div class="col-sm">
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Break">
                                </div>
                            </div>
                        </div>
                        <div class="form-group pt-3">
                            <textarea class="form-control" id="exampleFormControlTextarea1" rows="5" placeholder="Comment"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary rounded-pill">Submit</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Optional JavaScript; choose one of the two! -->

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

    <!-- Option 2: Separate Popper and Bootstrap JS -->
    <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>
    -->
</body>

</html>