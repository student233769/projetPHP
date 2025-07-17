        <div class="container mt-5">
            <h1>Cours disponible</h1>
            <div class="row" id="quotes-container">
                <?php if(count($liste_cours) > 0): ?>
                    <?php foreach($liste_cours as $cours): ?>
                        <?php echo '<div class="col-12 col-md-3 mb-3">' ?>
                    
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($cours['author']) ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($cours['content']) ?></p>
                            <p class="card-footer text-muted"><?php echo $cours['created_at'] ?></p>
                            <p class="text-muted">Posté par : <?php echo htmlspecialchars($cours['posted_by']) ?></p>
                            <p class="text-muted">Nombre de like : <?php echo get_nb_like($cours['quote_id']) ?></p>
                        </div>
                    </div>
                    <?php endforeach;  ?>
                <?php else: ?>
                    <p>Aucun cours disponible pour le moment.</p>
                <?php endif; ?>
            </div>
        </div>